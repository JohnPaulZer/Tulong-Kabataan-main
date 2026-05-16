<?php

namespace App\Http\Controllers;

use App\Models\IdentityStatus;
use App\Models\AdminAccount;
use App\Models\SiteSetting;
use App\Models\VerificationRequest;
use App\Notifications\ProviderQuotaExhaustedNotification;
use App\Notifications\VerificationDecisionNotification;
use App\Services\Verification\QuotaGuard;
use App\Services\Verification\VerificationAuditService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiditVerificationController
{
    public function __construct(
        private VerificationAuditService $audit,
        private QuotaGuard $quotaGuard,
    ) {
    }

    public function start(Request $request)
    {
        $user = $request->user();
        $cfg = (array) config('id_verification.providers.didit', []);
        $activeProvider = (string) SiteSetting::get('verification.provider', config('id_verification.provider', 'didit'));

        if (! SiteSetting::isTrue('verification.enabled')) {
            return back()->with('error', 'Account verification is currently disabled by the administrator.');
        }

        if ($activeProvider !== 'didit') {
            return redirect()->route('verify.page')
                ->with('message', 'The active verification provider is ' . $this->providerLabel($activeProvider) . '. Please use the document upload form.');
        }

        if (empty($cfg['api_key']) || empty($cfg['workflow_id'])) {
            return back()->with('error', 'Didit verification is not configured yet.');
        }

        $userId = (string) $user->user_id;
        $forceNewSession = $request->boolean('restart_didit') && app()->environment('local');

        $approved = VerificationRequest::where('user_id', $userId)
            ->where('status', VerificationRequest::STATUS_APPROVED)
            ->latest()
            ->first();

        if ($approved && ! $forceNewSession) {
            return redirect()->route('profile')
                ->with('message', 'Your account is already verified.');
        }

        if (! $forceNewSession) {
            $pending = VerificationRequest::where('user_id', $userId)
                ->where('provider_used', 'didit')
                ->where('status', VerificationRequest::STATUS_PENDING)
                ->latest()
                ->first();

            $pendingUrl = $pending ? $this->sessionUrl($pending->raw_provider_response) : null;

            if ($pending && $pending->provider_reference_id) {
                $decision = $this->fetchDecision((string) $pending->provider_reference_id);
                if ($decision) {
                    $updated = $this->applyDiditDecision($decision, 'poll');
                    if ($updated && $updated->status !== VerificationRequest::STATUS_PENDING) {
                        return redirect()->route('profile')
                            ->with('message', $this->userMessageForStatus((string) $updated->status));
                    }
                }
            }

            if ($pendingUrl) {
                return redirect()->away($pendingUrl);
            }
        }

        if (! $this->quotaGuard->reserve('didit')) {
            $this->switchProviderAfterQuotaExhausted('didit', 'ocr_space');

            return redirect()->route('verify.page')
                ->with('error', 'Didit has reached its free verification quota. The system switched to OCR.Space, so please submit using the document upload form.');
        }

        $reservedDiditQuota = true;
        $payload = $this->sessionPayload($request, $userId);

        try {
            $response = Http::withHeaders([
                'x-api-key' => (string) $cfg['api_key'],
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->timeout((int) ($cfg['timeout'] ?? 30))
                ->post(rtrim((string) ($cfg['base_url'] ?? 'https://verification.didit.me'), '/') . '/v3/session/', $payload);
        } catch (ConnectionException $e) {
            $this->refundDiditQuota($reservedDiditQuota);
            Log::warning('[Didit] Could not create session', ['error' => $e::class]);
            return back()->with('error', 'Could not reach Didit. Please try again.');
        } catch (\Throwable $e) {
            $this->refundDiditQuota($reservedDiditQuota);
            Log::warning('[Didit] Session creation failed', ['error' => $e::class]);
            return back()->with('error', 'Didit verification could not start. Please try again.');
        }

        if (! $response->successful()) {
            $this->refundDiditQuota($reservedDiditQuota);
            Log::warning('[Didit] Session creation returned HTTP error', [
                'status' => $response->status(),
                'body' => substr((string) $response->body(), 0, 500),
            ]);

            return back()->with('error', 'Didit rejected the verification request. Please check the workflow settings.');
        }

        $body = (array) $response->json();
        $sessionId = (string) ($body['session_id'] ?? '');
        $url = $this->sessionUrl($body);

        if ($sessionId === '' || ! $url) {
            $this->refundDiditQuota($reservedDiditQuota);
            Log::warning('[Didit] Session creation response missing required fields', [
                'keys' => array_keys($body),
            ]);

            return back()->with('error', 'Didit did not return a usable verification link.');
        }

        $supersededStatuses = [
            VerificationRequest::STATUS_PENDING,
            VerificationRequest::STATUS_REJECTED,
            VerificationRequest::STATUS_REUPLOAD,
        ];

        if ($forceNewSession) {
            $supersededStatuses[] = VerificationRequest::STATUS_APPROVED;
        }

        VerificationRequest::where('user_id', $userId)
            ->whereIn('status', $supersededStatuses)
            ->update(['status' => 'superseded']);

        $verificationRequest = VerificationRequest::create([
            'user_id' => $userId,
            'status' => VerificationRequest::STATUS_PENDING,
            'provider_used' => 'didit',
            'provider_reference_id' => $sessionId,
            'raw_provider_response' => $body,
            'decision_source' => VerificationRequest::SOURCE_AUTO,
            'decision_reason' => $forceNewSession
                ? 'New Didit test session created. Awaiting completion.'
                : 'Didit verification session created. Awaiting completion.',
            'review_notes' => $forceNewSession
                ? 'New Didit test session created. Awaiting completion.'
                : 'Didit verification session created. Awaiting completion.',
        ]);

        IdentityStatus::updateOrCreate(
            ['user_id' => $userId],
            ['status' => 'Pending']
        );

        $this->audit->record(
            $verificationRequest,
            'didit_session_created',
            'User started a Didit hosted verification session.',
            ['session_id' => $sessionId]
        );

        return redirect()->away($url);
    }

    public function callback(Request $request)
    {
        $sessionId = (string) (
            $request->query('verificationSessionId')
            ?: $request->query('session_id')
            ?: $request->query('sessionId')
            ?: ''
        );

        $payload = [
            'session_id' => $sessionId,
            'status' => (string) $request->query('status', ''),
            'callback_query' => $request->query(),
        ];

        if ($sessionId !== '') {
            $decision = $this->fetchDecision($sessionId);
            if ($decision) {
                $payload = array_merge($payload, $decision);
            }
        }

        $verificationRequest = $this->applyDiditDecision($payload, 'callback');

        if (! $verificationRequest) {
            return redirect()->route('verify.page')
                ->with('error', 'We could not match the Didit result to your account yet.');
        }

        $message = $this->userMessageForStatus((string) $verificationRequest->status);

        if (Auth::check()) {
            return redirect()->route('profile')->with('message', $message);
        }

        return redirect()->route('login.page')
            ->with('message', $message . ' Please log in to view your profile.');
    }

    public function webhook(Request $request)
    {
        $rawBody = $request->getContent();

        if (! $this->verifyWebhookSignature($request, $rawBody)) {
            Log::warning('[Didit] Rejected webhook with invalid signature', [
                'headers' => array_keys($request->headers->all()),
            ]);

            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $payload = json_decode($rawBody, true);
        if (! is_array($payload)) {
            return response()->json(['message' => 'Invalid JSON.'], 400);
        }

        $verificationRequest = $this->applyDiditDecision($payload, 'webhook');

        if (! $verificationRequest) {
            Log::warning('[Didit] Webhook could not be matched to a verification request', [
                'session_id' => data_get($payload, 'session_id'),
                'vendor_data' => data_get($payload, 'vendor_data'),
            ]);
        }

        return response()->json(['received' => true]);
    }

    private function refundDiditQuota(bool &$reserved): void
    {
        if (! $reserved) {
            return;
        }

        $this->quotaGuard->refund('didit');
        $reserved = false;
    }

    private function switchProviderAfterQuotaExhausted(string $exhaustedProvider, string $fallbackProvider): void
    {
        SiteSetting::set('verification.provider', $fallbackProvider, 'string', 'verification');
        SiteSetting::set('verification.enabled', true, 'bool', 'verification');

        $usage = $this->quotaGuard->usage($exhaustedProvider);
        $cacheKey = "id_verify_fallback_notified:{$exhaustedProvider}:" . now()->format('Ym');

        if (! Cache::has($cacheKey)) {
            Cache::put($cacheKey, true, now()->endOfMonth());
            $this->notifyAdminsQuotaExhausted($exhaustedProvider, $fallbackProvider, $usage);
        }

        Log::warning('[Didit] Provider quota exhausted; active provider switched', [
            'exhausted' => $exhaustedProvider,
            'fallback' => $fallbackProvider,
            'usage' => $usage,
        ]);
    }

    private function notifyAdminsQuotaExhausted(string $exhaustedProvider, string $fallbackProvider, array $usage): void
    {
        try {
            foreach (AdminAccount::all() as $admin) {
                $admin->notify(new ProviderQuotaExhaustedNotification(
                    $this->providerLabel($exhaustedProvider),
                    $this->providerLabel($fallbackProvider),
                    $usage
                ));
            }
        } catch (\Throwable $e) {
            Log::warning('[Didit] Failed to notify admins of provider quota exhaustion', [
                'error' => $e::class,
            ]);
        }
    }

    private function providerLabel(string $provider): string
    {
        return match ($provider) {
            'didit' => 'Didit',
            'ocr_space' => 'OCR.Space',
            'google_vision' => 'Google Vision',
            default => ucfirst(str_replace('_', ' ', $provider)),
        };
    }

    private function sessionPayload(Request $request, string $userId): array
    {
        $user = $request->user();

        $payload = [
            'workflow_id' => (string) config('id_verification.providers.didit.workflow_id'),
            'vendor_data' => $userId,
            'callback' => (string) (config('id_verification.providers.didit.callback_url') ?: route('verification.didit.callback')),
            'callback_method' => 'both',
            'language' => 'en',
            'contact_details' => [
                'email' => (string) $user->email,
                'send_notification_emails' => false,
                'email_lang' => 'en',
            ],
        ];

        $expectedDetails = array_filter([
            'first_name' => $user->first_name ?? null,
            'last_name' => $user->last_name ?? null,
            'date_of_birth' => $this->formatDate($user->birthday ?? null),
        ]);

        if ($expectedDetails) {
            $payload['expected_details'] = $expectedDetails;
        }

        return $payload;
    }

    private function fetchDecision(string $sessionId): ?array
    {
        $cfg = (array) config('id_verification.providers.didit', []);

        try {
            $response = Http::withHeaders([
                'x-api-key' => (string) $cfg['api_key'],
                'Accept' => 'application/json',
            ])->timeout((int) ($cfg['timeout'] ?? 30))
                ->get(rtrim((string) ($cfg['base_url'] ?? 'https://verification.didit.me'), '/') . "/v3/session/{$sessionId}/decision/");
        } catch (\Throwable $e) {
            Log::warning('[Didit] Could not fetch session decision', [
                'session_id' => $sessionId,
                'error' => $e::class,
            ]);

            return null;
        }

        if (! $response->successful()) {
            Log::info('[Didit] Decision fetch did not return success', [
                'session_id' => $sessionId,
                'status' => $response->status(),
            ]);

            return null;
        }

        $payload = $response->json();
        return is_array($payload) ? $payload : null;
    }

    private function applyDiditDecision(array $payload, string $source): ?VerificationRequest
    {
        $sessionId = (string) (data_get($payload, 'session_id') ?: data_get($payload, 'id') ?: '');
        $vendorData = (string) data_get($payload, 'vendor_data', '');
        $diditStatus = (string) (data_get($payload, 'decision.status') ?: data_get($payload, 'status', ''));

        $verificationRequest = null;

        if ($sessionId !== '') {
            $verificationRequest = VerificationRequest::where('provider_used', 'didit')
                ->where('provider_reference_id', $sessionId)
                ->latest()
                ->first();
        }

        if (! $verificationRequest && $vendorData !== '') {
            $verificationRequest = VerificationRequest::where('user_id', $vendorData)
                ->where('provider_used', 'didit')
                ->latest()
                ->first();
        }

        if (! $verificationRequest) {
            return null;
        }

        $previousStatus = (string) $verificationRequest->status;
        $localStatus = $this->localStatus($diditStatus);
        $reason = $this->decisionReason($diditStatus, $source);

        $verificationRequest->status = $localStatus;
        $verificationRequest->decision_source = VerificationRequest::SOURCE_AUTO;
        $verificationRequest->decision_reason = $reason;
        $verificationRequest->review_notes = $reason;
        $verificationRequest->raw_provider_response = $this->mergeRawProviderPayload(
            $verificationRequest->raw_provider_response,
            $payload,
            $source
        );

        if (in_array($localStatus, [VerificationRequest::STATUS_APPROVED, VerificationRequest::STATUS_REJECTED], true)) {
            $verificationRequest->reviewed_at = Carbon::now();
        }

        $verificationRequest->save();

        IdentityStatus::updateOrCreate(
            ['user_id' => $verificationRequest->user_id],
            ['status' => $this->identityStatus($localStatus)]
        );

        $this->audit->record(
            $verificationRequest,
            'didit_' . $source,
            $reason,
            [
                'session_id' => $sessionId ?: $verificationRequest->provider_reference_id,
                'didit_status' => $diditStatus,
            ]
        );

        $this->notifyIfFinalStatusChanged($verificationRequest, $previousStatus, $localStatus);

        return $verificationRequest;
    }

    private function verifyWebhookSignature(Request $request, string $rawBody): bool
    {
        $secret = (string) config('id_verification.providers.didit.webhook_secret', '');
        if ($secret === '') {
            return false;
        }

        $timestamp = $request->headers->get('X-Timestamp');
        if ($timestamp !== null && ctype_digit((string) $timestamp)) {
            if (abs(time() - (int) $timestamp) > 300) {
                return false;
            }
        }

        $checks = [];

        $rawSignature = $request->headers->get('X-Signature');
        if ($rawSignature) {
            $checks[$rawSignature] = hash_hmac('sha256', $rawBody, $secret);
        }

        $payload = json_decode($rawBody, true);
        if (is_array($payload)) {
            $signatureV2 = $request->headers->get('X-Signature-V2');
            if ($signatureV2) {
                $checks[$signatureV2] = hash_hmac('sha256', $this->canonicalJson($payload), $secret);
            }

            $simpleSignature = $request->headers->get('X-Signature-Simple');
            if ($simpleSignature) {
                $canonical = implode(':', [
                    data_get($payload, 'timestamp', ''),
                    data_get($payload, 'session_id', ''),
                    data_get($payload, 'status', ''),
                    data_get($payload, 'webhook_type', ''),
                ]);

                $checks[$simpleSignature] = hash_hmac('sha256', $canonical, $secret);
            }
        }

        foreach ($checks as $provided => $expected) {
            if (is_string($provided) && hash_equals($expected, $provided)) {
                return true;
            }
        }

        return false;
    }

    private function canonicalJson(array $payload): string
    {
        $sorted = $this->sortKeysRecursive($payload);

        return json_encode($sorted, JSON_UNESCAPED_SLASHES) ?: '{}';
    }

    private function sortKeysRecursive(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (array_is_list($value)) {
            return array_map(fn ($item) => $this->sortKeysRecursive($item), $value);
        }

        ksort($value);

        foreach ($value as $key => $item) {
            $value[$key] = $this->sortKeysRecursive($item);
        }

        return $value;
    }

    private function sessionUrl(mixed $payload): ?string
    {
        if (! is_array($payload)) {
            return null;
        }

        $url = $payload['url'] ?? $payload['verification_url'] ?? $payload['session_url'] ?? null;

        return is_string($url) && $url !== '' ? $url : null;
    }

    private function mergeRawProviderPayload(mixed $existing, array $payload, string $source): array
    {
        $raw = is_array($existing) ? $existing : [];
        $raw['last_' . $source] = $payload;
        $raw['last_status_at'] = Carbon::now()->toIso8601String();

        return $raw;
    }

    private function localStatus(string $diditStatus): string
    {
        return match (strtolower(trim($diditStatus))) {
            'approved', 'approve', 'success', 'successful', 'verified' => VerificationRequest::STATUS_APPROVED,
            'declined', 'decline', 'rejected', 'reject', 'failed', 'failure' => VerificationRequest::STATUS_REJECTED,
            default => VerificationRequest::STATUS_PENDING,
        };
    }

    private function identityStatus(string $localStatus): string
    {
        return match ($localStatus) {
            VerificationRequest::STATUS_APPROVED => 'Verified',
            VerificationRequest::STATUS_REJECTED => 'Rejected',
            VerificationRequest::STATUS_REUPLOAD => 'Reupload',
            default => 'Pending',
        };
    }

    private function decisionReason(string $diditStatus, string $source): string
    {
        $status = $diditStatus !== '' ? $diditStatus : 'Pending';

        return "Didit {$source} received. Verification status: {$status}.";
    }

    private function notifyIfFinalStatusChanged(VerificationRequest $request, string $previousStatus, string $localStatus): void
    {
        if ($previousStatus === $localStatus) {
            return;
        }

        if (! in_array($localStatus, [VerificationRequest::STATUS_APPROVED, VerificationRequest::STATUS_REJECTED], true)) {
            return;
        }

        try {
            $request->user?->notify(new VerificationDecisionNotification($request, $localStatus, $request->decision_reason));
        } catch (\Throwable $e) {
            Log::warning('[Didit] User notification failed', ['error' => $e::class]);
        }
    }

    private function userMessageForStatus(string $status): string
    {
        return match ($status) {
            VerificationRequest::STATUS_APPROVED => 'Didit approved your verification. Your account is now verified.',
            VerificationRequest::STATUS_REJECTED => 'Didit could not approve your verification. Please review your verification page.',
            default => 'Didit verification was received and is being reviewed.',
        };
    }

    private function formatDate(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
