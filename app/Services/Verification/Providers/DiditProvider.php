<?php

namespace App\Services\Verification\Providers;

use App\Services\Verification\Contracts\VerificationProvider;
use App\Services\Verification\OcrTextParser;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Didit Free KYC provider. Didit ships a free monthly tier that covers
 * ID document verification (authenticity), liveness, and face match.
 *
 * Important note on the implementation: Didit's public API has evolved
 * over time and the production endpoints are gated behind workflows you
 * configure in their dashboard. The integration here:
 *
 *   - Uploads the front (and optional back) of the ID to the configured
 *     Didit "verifications" endpoint with the configured workflow id.
 *   - Maps Didit's structured response back into our common shape.
 *
 * If your Didit account uses a different upload shape, this is the only
 * file you need to adapt — everything else in the pipeline reads from
 * the common shape returned by verify().
 */
class DiditProvider implements VerificationProvider
{
    public function __construct(private OcrTextParser $parser)
    {
    }

    public function name(): string
    {
        return 'didit';
    }

    public function isConfigured(): bool
    {
        $cfg = (array) config('id_verification.providers.didit', []);
        return ! empty($cfg['api_key']);
    }

    public function supportsAuthenticity(): bool
    {
        // Didit DOES support face matching and liveness, but it's delivered
        // asynchronously via webhook after the user completes the hosted
        // verification flow. Until the webhook integration is implemented,
        // we return false so the orchestrator doesn't promise auto-approval.
        // TODO: Change to true once DiditWebhookController is implemented.
        return false;
    }

    /**
     * Didit Free KYC provider.
     *
     * IMPORTANT: Didit uses a SESSION-BASED flow, not a direct file upload:
     *   1. POST /v3/session/ → creates a session, returns a verification URL
     *   2. User visits that URL to capture ID + selfie + liveness
     *   3. Didit sends results via webhook
     *
     * This means Didit CANNOT be used as a synchronous OCR call within our
     * pipeline. For a proper Didit integration you would need to:
     *   - Redirect the user to the Didit session URL
     *   - Handle the webhook callback with the verification results
     *   - Update the VerificationRequest asynchronously
     *
     * For now, this provider creates a session and returns the session URL.
     * If the workflow is configured for API-only (no hosted page), it sends
     * expected_details for cross-validation. The orchestrator will route
     * to manual review when Didit cannot produce an instant result.
     */
    public function verify(string $frontAbsolutePath, ?string $backAbsolutePath = null, array $context = []): array
    {
        $cfg = (array) config('id_verification.providers.didit', []);
        $apiKey = (string) ($cfg['api_key'] ?? '');
        $base   = rtrim((string) ($cfg['base_url'] ?? 'https://verification.didit.me'), '/');
        $workflow = (string) ($cfg['workflow_id'] ?? '');

        if ($apiKey === '') {
            return $this->failure('Didit API key not configured.');
        }
        if ($workflow === '') {
            return $this->failure('Didit workflow ID not configured.');
        }

        try {
            // Create a verification session. Didit will use the workflow's
            // configured steps (document, liveness, face match, etc.)
            $payload = [
                'workflow_id' => $workflow,
                'vendor_data' => (string) ($context['external_id'] ?? ''),
            ];

            // If we have the front image, send it as portrait_image for
            // face matching (base64, max 1MB per Didit docs)
            $frontBytes = @file_get_contents($frontAbsolutePath);
            if ($frontBytes !== false && strlen($frontBytes) <= 1024 * 1024) {
                $payload['portrait_image'] = base64_encode($frontBytes);
            }

            $response = Http::withHeaders([
                'x-api-key'    => $apiKey,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->timeout((int) ($cfg['timeout'] ?? 30))
              ->post($base . '/v3/session/', $payload);
        } catch (ConnectionException $e) {
            return $this->failure('Could not reach Didit.', $e);
        } catch (\Throwable $e) {
            return $this->failure('Didit request failed.', $e);
        }

        if (! $response->ok() && $response->status() !== 201) {
            Log::warning('[IdVerification][Didit] HTTP error', [
                'status' => $response->status(),
                'body'   => substr((string) $response->body(), 0, 500),
            ]);
            return $this->failure('Didit returned an error response (HTTP ' . $response->status() . ').');
        }

        $payload = (array) $response->json();
        $sessionUrl = $payload['url'] ?? null;
        $referenceId = $payload['session_id'] ?? null;

        // Didit is session-based — we can't get instant OCR results.
        // Return a partial success: the session was created, but we need
        // to wait for the webhook or redirect the user. For now, we
        // indicate that results are pending.
        return [
            'success'      => true,
            'provider'     => $this->name(),
            'reference_id' => $referenceId,
            'raw_text'     => '',
            'extracted'    => [
                'full_name'        => null,
                'birthdate'        => null,
                'id_number'        => null,
                'address'          => null,
                'expiration_date'  => null,
                'sex'              => null,
                'nationality'      => null,
                'id_type_detected' => null,
            ],
            'authenticity' => [
                'verified'   => null, // pending — will come via webhook
                'liveness'   => null,
                'face_match' => null,
                'score'      => null,
                'session_url' => $sessionUrl,
            ],
            'raw'          => $this->sanitizeRaw($payload),
            'error'        => null,
            'pending'      => true, // signals to orchestrator that results aren't instant
        ];
    }

    /**
     * Translate a Didit response into our common shape. Best-effort: we
     * read multiple possible field names because Didit returns slightly
     * different shapes depending on which workflow steps are enabled.
     */
    private function mapResponse(array $payload): array
    {
        $referenceId = $payload['session_id'] ?? $payload['id'] ?? $payload['verification_id'] ?? null;

        // Document data
        $document = $payload['document'] ?? $payload['id_verification']['document'] ?? [];
        $extracted = [
            'full_name'        => $this->compose($document, ['full_name', 'name']) ?: $this->joinNames($document),
            'birthdate'        => $this->isoDate($document['date_of_birth'] ?? $document['birth_date'] ?? null),
            'id_number'        => $document['document_number'] ?? $document['id_number'] ?? null,
            'address'          => $document['address'] ?? null,
            'expiration_date'  => $this->isoDate($document['expiration_date'] ?? $document['date_of_expiry'] ?? null),
            'sex'              => $document['sex'] ?? $document['gender'] ?? null,
            'nationality'      => $document['nationality'] ?? null,
            'id_type_detected' => $this->mapDocumentType($document['document_type'] ?? null),
        ];

        // Authenticity
        $idCheck = $payload['id_verification'] ?? [];
        $faceCheck = $payload['face_match'] ?? $payload['face_verification'] ?? [];
        $liveness = $payload['liveness'] ?? [];

        $authenticity = [
            'verified'   => $this->boolish($idCheck['status'] ?? $payload['status'] ?? null),
            'liveness'   => $this->boolish($liveness['status'] ?? null),
            'face_match' => $this->boolish($faceCheck['status'] ?? null),
            'score'      => isset($idCheck['confidence']) ? (float) $idCheck['confidence'] : null,
        ];

        $rawText = (string) ($document['raw_mrz'] ?? $document['raw_text'] ?? '');

        return [
            'success'      => true,
            'provider'     => $this->name(),
            'reference_id' => $referenceId,
            'raw_text'     => $rawText,
            'extracted'    => $extracted,
            'authenticity' => $authenticity,
            'raw'          => $this->sanitizeRaw($payload),
            'error'        => null,
        ];
    }

    private function compose(array $doc, array $keys): ?string
    {
        foreach ($keys as $k) {
            if (! empty($doc[$k])) {
                return (string) $doc[$k];
            }
        }
        return null;
    }

    private function joinNames(array $doc): ?string
    {
        $parts = array_filter([
            $doc['first_name'] ?? null,
            $doc['middle_name'] ?? null,
            $doc['last_name'] ?? $doc['surname'] ?? null,
        ]);
        return $parts ? implode(' ', $parts) : null;
    }

    private function isoDate(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        try {
            return (new \DateTimeImmutable($value))->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function boolish(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if ($value === null) {
            return null;
        }
        $s = strtolower((string) $value);
        if (in_array($s, ['approved', 'success', 'pass', 'passed', 'verified', 'true', 'ok'], true)) {
            return true;
        }
        if (in_array($s, ['rejected', 'failed', 'fail', 'declined', 'false'], true)) {
            return false;
        }
        return null;
    }

    private function mapDocumentType(?string $type): ?string
    {
        if (! $type) {
            return null;
        }
        $s = strtolower($type);
        if (str_contains($s, 'driver') || str_contains($s, 'license') || str_contains($s, 'licence')) {
            return OcrTextParser::TYPE_DRIVERS_LICENSE;
        }
        if (str_contains($s, 'national') || str_contains($s, 'philsys') || str_contains($s, 'identification card')) {
            return OcrTextParser::TYPE_PHILID;
        }
        return null;
    }

    /**
     * Strip large image-bytes / face-image payloads from the raw response
     * before persisting it. We keep structured fields only.
     */
    private function sanitizeRaw(array $payload): array
    {
        $clone = $payload;
        foreach (['face_image', 'document_image', 'document_front_image', 'document_back_image', 'selfie_image'] as $heavy) {
            if (isset($clone[$heavy])) {
                unset($clone[$heavy]);
            }
        }
        if (isset($clone['document']) && is_array($clone['document'])) {
            foreach (['front_image', 'back_image', 'photo'] as $heavy) {
                if (isset($clone['document'][$heavy])) {
                    unset($clone['document'][$heavy]);
                }
            }
        }
        return $clone;
    }

    private function failure(string $error, ?\Throwable $e = null): array
    {
        if ($e) {
            Log::warning('[IdVerification][Didit] failure', ['error' => $e::class]);
        }

        return [
            'success'      => false,
            'provider'     => $this->name(),
            'reference_id' => null,
            'raw_text'     => '',
            'extracted'    => [],
            'authenticity' => [],
            'raw'          => null,
            'error'        => $error,
        ];
    }
}
