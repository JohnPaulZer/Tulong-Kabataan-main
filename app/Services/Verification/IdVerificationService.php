<?php

namespace App\Services\Verification;

use App\Models\IdentityStatus;
use App\Models\User;
use App\Models\VerificationRequest;
use App\Notifications\VerificationDecisionNotification;
use App\Services\Storage\R2StorageService;
use App\Services\Verification\Contracts;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrator for the automated ID verification pipeline.
 *
 * Pipeline:
 *   1. Per-user attempt limit (defense in depth on top of throttle:upload)
 *   2. File signature / size validation
 *   3. Image quality (local, no external API)
 *   4. Provider call (Didit / OCR.Space / Google Vision / none)
 *   5. Rule-based fraud checks
 *   6. Score (compare extracted vs registered/typed)
 *   7. Decision: approved / pending (manual review) / rejected / reupload
 *   8. Persist + notify + audit
 *
 * The decision is written to the EXISTING status field so legacy admin
 * code keeps working. Detailed metadata (score, breakdown, fraud, etc.)
 * lives in the new fields on VerificationRequest.
 */
class IdVerificationService
{
    public const DECISION_APPROVED = 'approved';
    public const DECISION_MANUAL_REVIEW = 'manual_review';
    public const DECISION_REJECTED = 'rejected';
    public const DECISION_NEEDS_RESUBMISSION = 'needs_resubmission';

    public function __construct(
        private OcrService $ocrService,
        private ImageQualityService $imageQuality,
        private OcrTextParser $parser,
        private IdScoringService $scoring,
        private IdFraudCheckService $fraud,
        private VerificationAuditService $audit,
        private R2StorageService $storage,
        private QuotaGuard $quotaGuard,
    ) {
    }

    /**
     * Pre-upload validation + per-user attempt limiting. Throws
     * VerificationException with a friendly user message on failure.
     */
    public function validateUpload(UploadedFile $file, ?string $userId = null): void
    {
        $cfg = (array) config('id_verification.file', []);

        $maxBytes = ((int) ($cfg['max_size_mb'] ?? 5)) * 1024 * 1024;
        if ($file->getSize() > $maxBytes) {
            throw new VerificationException(
                'ID image is too large.',
                "Your ID image is too large. Please upload a photo under {$cfg['max_size_mb']} MB."
            );
        }

        $mime = (string) $file->getMimeType();
        $allowedMimes = (array) ($cfg['allowed_mimes'] ?? []);
        if (! empty($allowedMimes) && ! in_array($mime, $allowedMimes, true)) {
            throw new VerificationException(
                'Unsupported MIME type: ' . $mime,
                'Only JPG, JPEG, PNG, or WEBP images are accepted.'
            );
        }

        // Verify the actual file signature, not just the extension. We
        // check magic bytes for the four supported image formats.
        if (! $this->matchesImageSignature($file)) {
            throw new VerificationException(
                'File signature does not match an accepted image format.',
                'The uploaded file is not a real image. Please upload a JPG, PNG, or WEBP photo.'
            );
        }

        // Per-user daily attempt limit
        if ($userId) {
            $maxPerDay = (int) config('id_verification.max_attempts_per_day', 5);
            if ($maxPerDay > 0) {
                $cacheKey = 'id_verify_attempts:' . $userId;
                $attempts = (int) Cache::get($cacheKey, 0);
                if ($attempts >= $maxPerDay) {
                    throw new VerificationException(
                        'Daily verification attempt limit reached.',
                        'You have reached the maximum number of verification attempts for today. Please try again tomorrow.'
                    );
                }
                Cache::put($cacheKey, $attempts + 1, now()->endOfDay());
            }
        }
    }

    /**
     * Run the automated review for a freshly created VerificationRequest.
     * The request must already have its files uploaded to R2 and its
     * id_*_path fields populated.
     */
    public function runAutomatedReview(VerificationRequest $request, ?User $user = null): array
    {
        if (! filter_var(config('id_verification.enabled', true), FILTER_VALIDATE_BOOLEAN)) {
            // Automated review disabled — leave at "pending" for legacy admin flow.
            $this->audit->record($request, 'submitted', 'Automated review disabled — sent to admin queue.');
            return ['decision' => self::DECISION_MANUAL_REVIEW, 'score' => null];
        }

        $this->audit->record($request, 'submitted', 'Verification submitted by user.');

        // Resolve image bytes once (R2 -> temp file).
        $tempFront = null;
        $tempBack = null;

        try {
            $tempFront = $this->materializeFile($request->id_front_path);
            $tempBack  = $request->id_back_path
                ? $this->materializeFile($request->id_back_path)
                : null;

            if (! $tempFront) {
                $this->finalizeReupload(
                    $request,
                    'Could not read the uploaded ID image. Please re-upload.',
                    ['id_front']
                );
                return ['decision' => self::DECISION_NEEDS_RESUBMISSION, 'score' => null];
            }

            // 1) Image quality. Run on both sides — bad back-of-ID can be
            // a clue too (e.g. someone uploads a random photo for the back
            // because they didn't have one). The front's quality is the
            // gating signal; the back's report is informational.
            $quality = $this->imageQuality->analyze($tempFront);
            $request->image_quality_report = $quality;

            $backQuality = null;
            if ($tempBack) {
                $backQuality = $this->imageQuality->analyze($tempBack);
                $reportWithBack = is_array($quality) ? $quality : [];
                $reportWithBack['back'] = $backQuality;
                $request->image_quality_report = $reportWithBack;
            }

            // 2) Hashes for duplicate detection — front (primary) and back.
            $hash = @hash_file('sha256', $tempFront);
            if ($hash) {
                $request->id_front_hash = $hash;
            }
            $request->save();

            if (empty($quality['passed'])) {
                $reason = $quality['reason'] ?? 'Your ID image is not clear enough. Please upload a clearer photo of your valid ID.';
                $this->finalizeReupload(
                    $request,
                    'Your ID image is not clear enough. Please upload a clearer photo of your valid ID. (' . $reason . ')',
                    ['id_front']
                );
                return ['decision' => self::DECISION_NEEDS_RESUBMISSION, 'score' => 0];
            }

            // 3) Provider OCR / KYC.
            $provider = $this->ocrService->provider();
            $request->provider_used = $provider->name();

            // 3a) Reserve a slot from the provider's free-tier daily/monthly
            // quota BEFORE making the call. If we're out of budget for the
            // window, and the primary provider is Didit, automatically fall
            // back to OCR.Space (manual review mode) instead of blocking.
            $reserved = $this->quotaGuard->reserve($provider->name());
            if (! $reserved) {
                // Try fallback: if primary was Didit, switch to OCR.Space
                $fallbackProvider = $this->attemptProviderFallback($provider->name(), $request);
                if ($fallbackProvider) {
                    $provider = $fallbackProvider;
                    $request->provider_used = $provider->name();
                    // Reserve from the fallback provider's quota
                    $reserved = $this->quotaGuard->reserve($provider->name());
                    if (! $reserved) {
                        // Both providers exhausted — manual review
                        $this->finalizeManualReview(
                            $request,
                            'All verification provider quotas exhausted for this period. Submission sent to admin for manual review.'
                        );
                        return ['decision' => self::DECISION_MANUAL_REVIEW, 'score' => null];
                    }
                } else {
                    $this->finalizeManualReview(
                        $request,
                        'Daily/monthly automatic-check budget reached. Submission sent to admin for review.'
                    );
                    return ['decision' => self::DECISION_MANUAL_REVIEW, 'score' => null];
                }
            }

            // OCR.Space free tier caps at 1MB — preprocess if needed.
            $providerFront = $this->prepareForProvider($tempFront, $provider->name());
            $providerBack  = $tempBack ? $this->prepareForProvider($tempBack, $provider->name()) : null;

            $providerResult = $provider->verify(
                $providerFront,
                $providerBack,
                ['external_id' => (string) $request->getKey()]
            );

            // If the call fundamentally couldn't reach the provider (network
            // / config error), refund the reservation — we didn't actually
            // consume a quota unit. Provider-side processing errors that
            // produced a response still count as "used".
            if (empty($providerResult['success'])
                && in_array($providerResult['error'] ?? '', [
                    'No verification provider configured',
                    'OCR.Space API key not configured.',
                    'Google Vision API key not configured.',
                    'Didit API key not configured.',
                    'Could not reach OCR provider.',
                    'Could not reach Google Vision.',
                    'Could not reach Didit.',
                ], true)) {
                $this->quotaGuard->refund($provider->name());
            }

            // Persist OCR output (even if it failed, we want the audit trail).
            $extracted = (array) ($providerResult['extracted'] ?? []);
            $request->id_type_detected = $extracted['id_type_detected'] ?? null;
            $request->extracted_full_name = $this->sanitize($extracted['full_name'] ?? null);
            $request->extracted_birthdate = $extracted['birthdate'] ?? null;
            $request->extracted_id_number = $this->sanitize($extracted['id_number'] ?? null);
            $request->extracted_address = $this->sanitize($extracted['address'] ?? null);
            $request->extracted_expiration_date = $extracted['expiration_date'] ?? null;
            $request->extracted_sex = $this->sanitize($extracted['sex'] ?? null);
            $request->extracted_nationality = $this->sanitize($extracted['nationality'] ?? null);
            $request->provider_reference_id = $providerResult['reference_id'] ?? null;
            if (! empty($providerResult['raw'])) {
                $request->raw_provider_response = $providerResult['raw'];
            }
            $request->save();

            if (empty($providerResult['success'])) {
                // Provider failed — degrade to manual review.
                $this->finalizeManualReview(
                    $request,
                    'Automatic ID check could not complete (' . ($providerResult['error'] ?? 'provider error') . '). Sent to admin for review.'
                );
                return ['decision' => self::DECISION_MANUAL_REVIEW, 'score' => null];
            }

            // Didit is session-based: it creates a session but results come
            // asynchronously via webhook. When 'pending' is flagged, we can't
            // score yet — route to manual review and note the session ID.
            if (! empty($providerResult['pending'])) {
                $sessionUrl = $providerResult['authenticity']['session_url'] ?? null;
                $note = 'Didit verification session created (ID: ' . ($providerResult['reference_id'] ?? 'unknown') . ').';
                if ($sessionUrl) {
                    $note .= ' Awaiting user completion or webhook results.';
                }
                $note .= ' Sent to admin for manual review in the meantime.';
                $this->finalizeManualReview($request, $note);
                return ['decision' => self::DECISION_MANUAL_REVIEW, 'score' => null];
            }

            // 4) Fraud checks.
            $warnings = $this->fraud->evaluate(
                $request,
                $providerResult,
                $request->id_front_hash,
                (string) $request->user_id
            );
            $request->fraud_warnings = $warnings;
            $request->save();

            // 5) Score.
            $scored = $this->scoring->score($request, $user, $providerResult, $quality);
            $request->confidence_score = $scored['score'];
            $request->score_breakdown = $scored['breakdown'];
            $request->save();

            // 6) Decision.
            $decision = $this->decide($request, $scored, $warnings, $providerResult);

            return ['decision' => $decision, 'score' => $scored['score']];
        } catch (\Throwable $e) {
            Log::error('[IdVerification] Pipeline crashed', [
                'request_id' => (string) $request->getKey(),
                'error' => $e::class,
                'message' => $e->getMessage(),
            ]);
            $this->finalizeManualReview(
                $request,
                'Automated review failed unexpectedly. Submission sent to admin for manual review.'
            );
            return ['decision' => self::DECISION_MANUAL_REVIEW, 'score' => null];
        } finally {
            foreach ([$tempFront, $tempBack] as $tmp) {
                if ($tmp && is_file($tmp)) {
                    @unlink($tmp);
                }
            }
        }
    }

    // -----------------------------------------------------------------
    // Decision finalizers
    // -----------------------------------------------------------------

    private function decide(VerificationRequest $request, array $scored, array $warnings, array $providerResult): string
    {
        $autoApprove = (int) config('id_verification.scoring.auto_approve', 85);
        $manualReview = (int) config('id_verification.scoring.manual_review', 60);

        $highestSeverity = IdFraudCheckService::highestSeverity($warnings);
        $score = $scored['score'];

        // Liveness requirement: when require_liveness=true and the provider
        // either does not support liveness or did not pass it, we MUST NOT
        // auto-approve.
        $liveness = $providerResult['authenticity']['liveness'] ?? null;
        $livenessOk = $liveness === true;
        $livenessRequired = (bool) config('id_verification.require_liveness', false);

        // Hard reject path
        if ($highestSeverity === IdFraudCheckService::SEVERITY_REJECT) {
            return $this->finalizeRejected($request, $warnings, $scored);
        }

        // Face-match safety net: when the active provider does NOT support
        // face matching (OCR.Space, Google Vision), we CANNOT verify that
        // the selfie shows the same person as the ID photo. In that case,
        // never auto-approve — always route to admin for visual confirmation
        // of the selfie. This prevents someone uploading a valid ID that
        // belongs to another person.
        $actualProviderName = $request->provider_used ?? '';
        $actualProvider = $this->ocrService->provider($actualProviderName);
        $providerSupportsFaceMatch = $actualProvider->supportsAuthenticity();
        $faceMatchPassed = ! empty($providerResult['authenticity']['face_match']);

        if (! $providerSupportsFaceMatch && $score >= $autoApprove
            && $highestSeverity !== IdFraudCheckService::SEVERITY_MANUAL) {
            // Score is high enough for auto-approve, but we can't verify
            // the selfie matches the ID. Send to admin with a clear note.
            return $this->finalizeManualReview(
                $request,
                'Score is high (' . $score . '/100) but face match cannot be verified automatically. '
                . 'Admin must visually confirm the selfie matches the ID photo.'
            );
        }

        // Auto-approve path (only reachable when provider supports face match
        // AND the match passed, OR when liveness is not required).
        if ($score >= $autoApprove
            && $highestSeverity !== IdFraudCheckService::SEVERITY_MANUAL
            && (! $livenessRequired || $livenessOk)
            && ($providerSupportsFaceMatch ? $faceMatchPassed : true)
        ) {
            return $this->finalizeApproved($request, $scored);
        }

        // Resubmission for low-quality / unreadable cases
        if ($score < $manualReview && empty($scored['reasons']) === false) {
            // If reasons mention readability / quality / cropping, ask for resubmission
            $needsResubmit = false;
            foreach ($scored['reasons'] as $r) {
                $lc = strtolower((string) $r);
                if (str_contains($lc, 'could not be read') || str_contains($lc, 'too dark')
                    || str_contains($lc, 'too bright') || str_contains($lc, 'blurry')
                    || str_contains($lc, 'too small') || str_contains($lc, 'not look like an id')) {
                    $needsResubmit = true;
                    break;
                }
            }
            if ($needsResubmit) {
                $this->finalizeReupload(
                    $request,
                    'Your ID image is not clear enough. ' . $this->joinReasons($scored['reasons']),
                    ['id_front']
                );
                return self::DECISION_NEEDS_RESUBMISSION;
            }
        }

        // Otherwise: manual review (covers the middle band AND any
        // SEVERITY_MANUAL fraud signals we picked up).
        return $this->finalizeManualReview(
            $request,
            $this->buildManualReason($scored, $warnings)
        );
    }

    private function finalizeApproved(VerificationRequest $request, array $scored): string
    {
        $request->status = VerificationRequest::STATUS_APPROVED;
        $request->decision_source = VerificationRequest::SOURCE_AUTO;
        $request->decision_reason = 'Automatically approved (confidence ' . $request->confidence_score . '/100).';
        $request->reviewed_at = Carbon::now();
        $request->reupload_fields = null;
        $request->review_notes = $request->decision_reason;
        $request->save();

        IdentityStatus::updateOrCreate(
            ['user_id' => $request->user_id],
            ['status' => 'Verified']
        );

        $this->audit->record($request, 'auto_approved', $request->decision_reason, [
            'breakdown' => $scored['breakdown'] ?? [],
        ]);

        $this->notifyUser($request, 'approved', $request->decision_reason);

        return self::DECISION_APPROVED;
    }

    private function finalizeManualReview(VerificationRequest $request, string $reason): string
    {
        $request->status = VerificationRequest::STATUS_PENDING;
        $request->decision_source = VerificationRequest::SOURCE_AUTO;
        $request->decision_reason = $reason;
        $request->save();

        IdentityStatus::updateOrCreate(
            ['user_id' => $request->user_id],
            ['status' => 'Pending']
        );

        $this->audit->record($request, 'manual_review', $reason);

        return self::DECISION_MANUAL_REVIEW;
    }

    private function finalizeRejected(VerificationRequest $request, array $warnings, array $scored): string
    {
        $reasons = IdFraudCheckService::reasons($warnings);
        $reasonText = $this->joinReasons($reasons) ?: 'ID could not be verified.';

        $request->status = VerificationRequest::STATUS_REJECTED;
        $request->decision_source = VerificationRequest::SOURCE_AUTO;
        $request->decision_reason = $reasonText;
        $request->reviewed_at = Carbon::now();
        $request->review_notes = $reasonText;
        $request->save();

        IdentityStatus::updateOrCreate(
            ['user_id' => $request->user_id],
            ['status' => 'Rejected']
        );

        $this->audit->record($request, 'auto_rejected', $reasonText, [
            'warnings' => $warnings,
            'score' => $scored['score'] ?? null,
        ]);

        $this->notifyUser($request, 'rejected', $reasonText);

        return self::DECISION_REJECTED;
    }

    private function finalizeReupload(VerificationRequest $request, string $reason, array $fields = ['id_front']): string
    {
        $request->status = VerificationRequest::STATUS_REUPLOAD;
        $request->decision_source = VerificationRequest::SOURCE_AUTO;
        $request->decision_reason = $reason;
        $request->reupload_fields = $fields;
        $request->review_notes = $reason;
        $request->save();

        IdentityStatus::updateOrCreate(
            ['user_id' => $request->user_id],
            ['status' => 'Reupload']
        );

        $this->audit->record($request, 'resubmission_requested', $reason, [
            'fields' => $fields,
        ]);

        $this->notifyUser($request, 'request_reupload', $reason);

        return self::DECISION_NEEDS_RESUBMISSION;
    }

    // -----------------------------------------------------------------
    // Utilities
    // -----------------------------------------------------------------

    private function notifyUser(VerificationRequest $request, string $decision, ?string $notes): void
    {
        try {
            $user = $request->user;
            if ($user) {
                $user->notify(new VerificationDecisionNotification($request, $decision, $notes));
            }
        } catch (\Throwable $e) {
            Log::warning('[IdVerification] User notification failed', [
                'error' => $e::class,
            ]);
        }
    }

    private function buildManualReason(array $scored, array $warnings): string
    {
        $parts = [];
        $parts[] = 'Needs admin review because confidence is not high enough (' . $scored['score'] . '/100).';
        $reasons = IdFraudCheckService::reasons($warnings);
        if ($reasons) {
            $parts[] = $this->joinReasons($reasons);
        } elseif (! empty($scored['reasons'])) {
            $parts[] = $this->joinReasons($scored['reasons']);
        }
        return trim(implode(' ', $parts));
    }

    private function joinReasons(array $reasons): string
    {
        $reasons = array_values(array_filter(array_map('trim', $reasons), fn ($r) => $r !== ''));
        return implode(' ', $reasons);
    }

    private function sanitize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        // Strip control chars but preserve unicode characters.
        $value = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;
        return mb_substr($value, 0, 255);
    }

    /**
     * Pull a stored R2 file down to a temporary local path so providers
     * (and our local image quality / hashing) can read raw bytes. Returns
     * null if the file could not be retrieved.
     */
    private function materializeFile(?string $key): ?string
    {
        if (! $key) {
            return null;
        }

        $contents = $this->storage->get($key);
        if ($contents === null) {
            return null;
        }

        $tmp = tempnam(sys_get_temp_dir(), 'idv_');
        if ($tmp === false) {
            return null;
        }
        file_put_contents($tmp, $contents);
        return $tmp;
    }

    /**
     * Some providers have strict file size limits (OCR.Space free tier
     * is 1MB) and some can't read certain formats (OCR.Space does NOT
     * support WebP). When required, downscale and/or re-encode a JPEG /
     * PNG / WEBP into something the provider accepts. Returns a path
     * that may be the original (if no work was needed) or a new temp
     * file.
     */
    private function prepareForProvider(string $absolutePath, string $providerName): string
    {
        // Per-provider constraints. OCR.Space rejects WebP and TIFF on
        // the free tier, so we always re-encode anything that isn't
        // already a JPEG/PNG. Google Vision accepts WebP natively, so
        // we only resize there (no re-encode needed).
        $maxKb = match ($providerName) {
            'ocr_space' => (int) config('id_verification.providers.ocr_space.max_upload_kb', 1024),
            default     => 0,
        };

        $sourceFormat = $this->detectImageFormat($absolutePath);
        $forceReencode = $providerName === 'ocr_space'
            && ! in_array($sourceFormat, ['jpeg', 'png'], true);

        $bytes = @filesize($absolutePath);
        $needsResize = $maxKb > 0 && $bytes !== false && $bytes > ($maxKb * 1024);

        if (! $forceReencode && ! $needsResize) {
            return $absolutePath;
        }

        if (! function_exists('imagecreatefromstring')) {
            return $absolutePath;
        }

        $data = @file_get_contents($absolutePath);
        if ($data === false) {
            return $absolutePath;
        }

        $img = @imagecreatefromstring($data);
        if (! $img) {
            return $absolutePath;
        }

        $tmp = tempnam(sys_get_temp_dir(), 'idv_resized_');
        // Step quality / dimensions down until we fit the limit (or run out
        // of room to shrink). JPEG keeps the smallest footprint, so we
        // re-encode to JPEG even if the input was PNG/WEBP.
        $width = imagesx($img);
        $height = imagesy($img);
        $quality = 90;

        for ($attempt = 0; $attempt < 6; $attempt++) {
            $copy = imagecreatetruecolor($width, $height);
            imagecopyresampled($copy, $img, 0, 0, 0, 0, $width, $height, imagesx($img), imagesy($img));
            imagejpeg($copy, $tmp, $quality);
            imagedestroy($copy);

            $size = @filesize($tmp);
            if (! $needsResize) {
                // Re-encode only — first pass is enough.
                break;
            }
            if ($size !== false && $size <= ($maxKb * 1024)) {
                break;
            }
            // Shrink dimensions and try again
            $width  = (int) ($width * 0.85);
            $height = (int) ($height * 0.85);
            $quality = max(40, $quality - 10);
            if ($width < 320 || $height < 200) {
                break;
            }
        }

        imagedestroy($img);
        return $tmp;
    }

    /**
     * Detect image format from magic bytes. Lighter than getimagesize()
     * and works without the fileinfo extension.
     */
    private function detectImageFormat(string $path): ?string
    {
        $h = @fopen($path, 'rb');
        if (! $h) {
            return null;
        }
        $header = fread($h, 16) ?: '';
        fclose($h);

        if (str_starts_with($header, "\xFF\xD8\xFF")) {
            return 'jpeg';
        }
        if (str_starts_with($header, "\x89PNG\r\n\x1A\n")) {
            return 'png';
        }
        if (str_starts_with($header, 'RIFF') && substr($header, 8, 4) === 'WEBP') {
            return 'webp';
        }
        if (str_starts_with($header, 'GIF8')) {
            return 'gif';
        }
        return null;
    }

    /**
     * When the primary provider's quota is exhausted, attempt to fall back
     * to a secondary provider. The main use case: Didit (500 free/month)
     * runs out → switch to OCR.Space (manual review mode) and alert the
     * admin exactly once per exhaustion window.
     *
     * Returns the fallback VerificationProvider instance, or null if no
     * fallback is available / configured.
     */
    private function attemptProviderFallback(string $exhaustedProvider, VerificationRequest $request): ?Contracts\VerificationProvider
    {
        // Define the fallback chain. Only Didit → OCR.Space is meaningful
        // today; extend this array if more providers are added later.
        $fallbackMap = [
            'didit'         => 'ocr_space',
            'ocr_space'     => 'google_vision',
            'google_vision' => null,
        ];

        $fallbackKey = $fallbackMap[$exhaustedProvider] ?? null;
        if (! $fallbackKey) {
            return null;
        }

        $fallback = $this->ocrService->provider($fallbackKey);
        if (! $fallback->isConfigured()) {
            return null;
        }

        // Alert the admin ONCE per calendar month that the primary provider
        // quota was exhausted and we've switched to the fallback. We use a
        // cache flag so the notification doesn't fire on every single request.
        $cacheKey = "id_verify_fallback_notified:{$exhaustedProvider}:" . now()->format('Ym');
        if (! Cache::has($cacheKey)) {
            Cache::put($cacheKey, true, now()->endOfMonth());
            $this->notifyAdminQuotaExhausted($exhaustedProvider, $fallbackKey);
        }

        // Log the switch for the audit trail.
        $this->audit->record($request, 'provider_fallback', sprintf(
            '%s monthly quota exhausted — falling back to %s (manual review mode).',
            ucfirst(str_replace('_', ' ', $exhaustedProvider)),
            ucfirst(str_replace('_', ' ', $fallbackKey))
        ));

        return $fallback;
    }

    /**
     * Send a one-time notification to all admin accounts that the primary
     * verification provider's free quota has been used up for the month.
     */
    private function notifyAdminQuotaExhausted(string $exhaustedProvider, string $fallbackProvider): void
    {
        try {
            $usage = $this->quotaGuard->usage($exhaustedProvider);
            $providerName = ucfirst(str_replace('_', ' ', $exhaustedProvider));
            $fallbackName = ucfirst(str_replace('_', ' ', $fallbackProvider));

            // Use the DatabaseNotification model directly to push a notice
            // into every admin's notification feed. We target AdminAccount
            // if it's notifiable, otherwise log it.
            $admins = \App\Models\AdminAccount::all();
            foreach ($admins as $admin) {
                if (method_exists($admin, 'notify')) {
                    $admin->notify(new \App\Notifications\ProviderQuotaExhaustedNotification(
                        $providerName,
                        $fallbackName,
                        $usage
                    ));
                }
            }

            Log::warning('[IdVerification] Provider quota exhausted — admin notified', [
                'exhausted' => $exhaustedProvider,
                'fallback'  => $fallbackProvider,
                'usage'     => $usage,
            ]);
        } catch (\Throwable $e) {
            // Notification failures must never block the verification flow.
            Log::warning('[IdVerification] Failed to notify admin of quota exhaustion', [
                'error' => $e::class,
            ]);
        }
    }

    /**
     * Verify the file's magic bytes match a JPG/PNG/WEBP signature so we
     * don't blindly trust the client-supplied MIME / extension.
     */
    private function matchesImageSignature(UploadedFile $file): bool
    {
        $path = $file->getRealPath();
        if (! $path || ! is_file($path)) {
            return false;
        }
        $h = @fopen($path, 'rb');
        if (! $h) {
            return false;
        }
        $header = fread($h, 16) ?: '';
        fclose($h);

        // JPEG: FF D8 FF
        if (str_starts_with($header, "\xFF\xD8\xFF")) {
            return true;
        }
        // PNG: 89 50 4E 47 0D 0A 1A 0A
        if (str_starts_with($header, "\x89PNG\r\n\x1A\n")) {
            return true;
        }
        // WEBP: "RIFF" .... "WEBP"
        if (str_starts_with($header, 'RIFF') && substr($header, 8, 4) === 'WEBP') {
            return true;
        }
        return false;
    }
}
