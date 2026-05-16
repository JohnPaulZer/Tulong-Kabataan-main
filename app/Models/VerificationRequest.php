<?php

namespace App\Models;

use App\Models\Concerns\EncryptsSensitiveAttributes;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * VerificationRequest stores both the user-submitted KYC data AND the
 * results of the automated review pipeline (image quality, OCR, scoring,
 * fraud signals). The legacy admin-only flow is preserved by keeping the
 * core "status" field (pending/approved/rejected/reupload) — automated
 * decisions write through to that field while populating the new
 * decision_* / extracted_* / score columns for context.
 */
class VerificationRequest extends Model
{
    use EncryptsSensitiveAttributes, HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'verification_requests';

    /**
     * Internal status taxonomy used by the automated pipeline. The legacy
     * statuses (pending/approved/rejected/reupload) remain the canonical
     * values stored in $status so existing admin views keep working. The
     * extra granularity is captured by decision_reason / decision_source.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_REUPLOAD = 'reupload';

    public const SOURCE_AUTO = 'auto';
    public const SOURCE_ADMIN = 'admin';

    protected $fillable = [
        // Submitted by the user
        'user_id',
        'id_type',
        'id_number',
        'id_number_hash',
        'dob',
        'sex',
        'first_name',
        'middle_name',
        'last_name',
        'address',
        'id_expiry',
        'id_front_path',
        'id_back_path',
        'face_photo_path',
        'selfie_path',
        'supporting_doc_path',

        // Existing manual flow
        'rule_flags',
        'reupload_fields',
        'status',
        'review_notes',

        // === New automated review fields ===
        'id_type_detected',
        'id_front_hash',
        'extracted_full_name',
        'extracted_birthdate',
        'extracted_address',
        'extracted_id_number',
        'extracted_expiration_date',
        'extracted_sex',
        'extracted_nationality',
        'confidence_score',
        'score_breakdown',
        'decision_source',
        'decision_reason',
        'fraud_warnings',
        'image_quality_report',
        'provider_used',
        'provider_reference_id',
        'raw_provider_response',
        'reviewed_by_admin_id',
        'reviewed_at',
    ];

    protected $casts = [
        'dob' => 'date',
        'id_expiry' => 'date',
        'rule_flags' => 'array',
        'reupload_fields' => 'array',
        'score_breakdown' => 'array',
        'fraud_warnings' => 'array',
        'image_quality_report' => 'array',
        'extracted_birthdate' => 'date',
        'extracted_expiration_date' => 'date',
        'reviewed_at' => 'datetime',
        'confidence_score' => 'integer',
    ];

    public function getRequestIdAttribute()
    {
        return (string) ($this->attributes['_id'] ?? $this->getKey());
    }

    // -----------------------------------------------------------------
    // Encrypted sensitive attributes
    // -----------------------------------------------------------------

    public function getIdNumberAttribute($value)
    {
        return $this->decryptSensitiveValue($value);
    }

    public function setIdNumberAttribute($value): void
    {
        $this->attributes['id_number'] = $this->encryptSensitiveValue($value);
    }

    public function getAddressAttribute($value)
    {
        return $this->decryptSensitiveValue($value);
    }

    public function setAddressAttribute($value): void
    {
        $this->attributes['address'] = $this->encryptSensitiveValue($value);
    }

    public function getReviewNotesAttribute($value)
    {
        return $this->decryptSensitiveValue($value);
    }

    public function setReviewNotesAttribute($value): void
    {
        $this->attributes['review_notes'] = $this->encryptSensitiveValue($value);
    }

    public function getExtractedFullNameAttribute($value)
    {
        return $this->decryptSensitiveValue($value);
    }

    public function setExtractedFullNameAttribute($value): void
    {
        $this->attributes['extracted_full_name'] = $this->encryptSensitiveValue($value);
    }

    public function getExtractedAddressAttribute($value)
    {
        return $this->decryptSensitiveValue($value);
    }

    public function setExtractedAddressAttribute($value): void
    {
        $this->attributes['extracted_address'] = $this->encryptSensitiveValue($value);
    }

    public function getExtractedIdNumberAttribute($value)
    {
        return $this->decryptSensitiveValue($value);
    }

    public function setExtractedIdNumberAttribute($value): void
    {
        $this->attributes['extracted_id_number'] = $this->encryptSensitiveValue($value);
    }

    public function getRawProviderResponseAttribute($value)
    {
        $decrypted = $this->decryptSensitiveValue($value);

        if ($decrypted === null || $decrypted === '') {
            return null;
        }

        // Stored as JSON inside the encrypted blob so we can return an array
        // for use in admin views without decrypting twice.
        $decoded = json_decode((string) $decrypted, true);
        return is_array($decoded) ? $decoded : $decrypted;
    }

    public function setRawProviderResponseAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['raw_provider_response'] = null;
            return;
        }

        $payload = is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->attributes['raw_provider_response'] = $this->encryptSensitiveValue($payload);
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    /**
     * Whether this verification was finalized by the automated pipeline
     * (as opposed to being touched by an admin afterwards).
     */
    public function wasAutoDecided(): bool
    {
        return $this->decision_source === self::SOURCE_AUTO;
    }

    public function isIncompleteDiditSession(): bool
    {
        if (($this->provider_used ?? null) !== 'didit' || ($this->status ?? null) !== self::STATUS_PENDING) {
            return false;
        }

        foreach ([
            'id_type',
            'id_number',
            'id_number_hash',
            'id_front_path',
            'id_back_path',
            'face_photo_path',
            'selfie_path',
            'extracted_id_number',
        ] as $attribute) {
            if (filled($this->{$attribute} ?? null)) {
                return false;
            }
        }

        $raw = $this->raw_provider_response;
        if (! is_array($raw)) {
            return true;
        }

        $providerEvents = [];
        foreach (['last_callback', 'last_webhook', 'last_poll'] as $providerEvent) {
            if (array_key_exists($providerEvent, $raw) && is_array($raw[$providerEvent])) {
                $providerEvents[] = $raw[$providerEvent];
            }
        }

        if (! $providerEvents) {
            return true;
        }

        foreach ($providerEvents as $payload) {
            if (filled(data_get($payload, 'document')) || filled(data_get($payload, 'id_verification'))) {
                return false;
            }

            $status = strtolower(trim((string) (
                data_get($payload, 'decision.status')
                ?: data_get($payload, 'status', '')
            )));

            if ($status !== '' && ! in_array($status, [
                'created',
                'new',
                'not_started',
                'not started',
                'started',
                'in_progress',
                'in progress',
                'expired',
                'cancelled',
                'canceled',
            ], true)) {
                return false;
            }
        }

        return true;
    }

    public function fillDiditCredentialSnapshot(array $payload): void
    {
        $snapshot = self::diditCredentialSnapshotFromPayload($payload);

        if (! $snapshot) {
            return;
        }

        $this->id_type_detected = $snapshot['document_type_code'] ?? $snapshot['document_type'] ?? $this->id_type_detected;
        $this->extracted_full_name = $snapshot['full_name'] ?? $this->extracted_full_name;
        $this->extracted_birthdate = $snapshot['birthdate'] ?? $this->extracted_birthdate;
        $this->extracted_address = $snapshot['formatted_address'] ?? $snapshot['address'] ?? $this->extracted_address;
        $this->extracted_id_number = $snapshot['document_number'] ?? $snapshot['personal_number'] ?? $this->extracted_id_number;
        $this->extracted_expiration_date = $snapshot['expiration_date'] ?? $this->extracted_expiration_date;
        $this->extracted_sex = $snapshot['gender'] ?? $this->extracted_sex;
        $this->extracted_nationality = $snapshot['nationality'] ?? $this->extracted_nationality;

        $diditScores = array_filter([
            $this->scoreToPercent($snapshot['face_match_score'] ?? null),
            $this->scoreToPercent($snapshot['liveness_score'] ?? null),
            $this->scoreToPercent($snapshot['front_image_quality_score'] ?? null),
            $this->scoreToPercent($snapshot['back_image_quality_score'] ?? null),
        ], fn ($score) => $score !== null);

        if ($this->confidence_score === null && $diditScores) {
            $this->confidence_score = (int) round(array_sum($diditScores) / count($diditScores));
        }

        $breakdown = is_array($this->score_breakdown) ? $this->score_breakdown : [];
        $breakdown['didit'] = array_filter([
            'decision_status' => $snapshot['decision_status'] ?? null,
            'id_status' => $snapshot['id_status'] ?? null,
            'liveness_status' => $snapshot['liveness_status'] ?? null,
            'face_match_status' => $snapshot['face_match_status'] ?? null,
            'document_type' => $snapshot['document_type'] ?? null,
            'document_number_present' => filled($snapshot['document_number'] ?? null),
            'face_match_score' => $snapshot['face_match_score'] ?? null,
            'liveness_score' => $snapshot['liveness_score'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');

        $this->score_breakdown = $breakdown;
    }

    public function diditCredentialSnapshot(): array
    {
        if (($this->provider_used ?? null) !== 'didit') {
            return [];
        }

        $raw = $this->raw_provider_response;
        if (! is_array($raw)) {
            return [];
        }

        $payloads = array_filter([
            data_get($raw, 'last_callback'),
            data_get($raw, 'last_poll'),
            data_get($raw, 'last_webhook.decision'),
            data_get($raw, 'last_webhook'),
            $raw,
        ], 'is_array');

        foreach ($payloads as $payload) {
            $snapshot = self::diditCredentialSnapshotFromPayload($payload);
            if ($snapshot) {
                return $snapshot;
            }
        }

        return [];
    }

    public static function diditCredentialSnapshotFromPayload(array $payload): array
    {
        $id = self::firstArrayFromPayload($payload, [
            'id_verifications.0',
            'id_verification',
            'decision.id_verification',
            'document',
        ]);

        if (! $id) {
            return [];
        }

        $liveness = self::firstArrayFromPayload($payload, [
            'liveness_checks.0',
            'liveness',
            'decision.liveness',
        ]);
        $faceMatch = self::firstArrayFromPayload($payload, [
            'face_matches.0',
            'face_match',
            'decision.face_match',
        ]);
        $decision = self::firstArrayFromPayload($payload, [
            'decision',
        ]);

        $firstName = self::firstFilledValue($id, ['first_name']);
        $middleName = self::firstFilledValue($id, ['middle_name', 'extra_fields.middle_name']);
        $lastName = self::firstFilledValue($id, ['last_name', 'surname', 'extra_fields.first_surname']);
        $fullName = self::firstFilledValue($id, ['full_name', 'name'])
            ?: trim(implode(' ', array_filter([$firstName, $middleName, $lastName])));
        $documentType = self::firstFilledValue($id, ['document_type', 'type']);

        return array_filter([
            'decision_status' => self::firstFilledValue($decision, ['status']),
            'id_status' => self::firstFilledValue($id, ['status']),
            'document_type' => $documentType,
            'document_type_code' => self::normalizeDiditDocumentType($documentType),
            'document_number' => self::firstFilledValue($id, ['document_number', 'id_number', 'number']),
            'personal_number' => self::firstFilledValue($id, ['personal_number']),
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'full_name' => $fullName !== '' ? $fullName : null,
            'birthdate' => self::normalizeDiditDate(self::firstFilledValue($id, ['date_of_birth', 'birth_date', 'dob'])),
            'age' => self::firstFilledValue($id, ['age']),
            'gender' => self::normalizeDiditGender(self::firstFilledValue($id, ['gender', 'sex'])),
            'address' => self::firstFilledValue($id, ['address']),
            'formatted_address' => self::firstFilledValue($id, ['formatted_address']),
            'place_of_birth' => self::firstFilledValue($id, ['place_of_birth']),
            'nationality' => self::firstFilledValue($id, ['nationality']),
            'expiration_date' => self::normalizeDiditDate(self::firstFilledValue($id, ['expiration_date', 'date_of_expiry'])),
            'issue_date' => self::normalizeDiditDate(self::firstFilledValue($id, ['date_of_issue', 'issue_date'])),
            'issuing_state' => self::firstFilledValue($id, ['issuing_state']),
            'issuing_state_name' => self::firstFilledValue($id, ['issuing_state_name']),
            'liveness_status' => self::firstFilledValue($liveness, ['status']),
            'liveness_method' => self::firstFilledValue($liveness, ['method']),
            'liveness_score' => self::firstFilledValue($liveness, ['score']),
            'face_match_status' => self::firstFilledValue($faceMatch, ['status']),
            'face_match_score' => self::firstFilledValue($faceMatch, ['score']),
            'front_image_quality_score' => self::firstFilledValue($id, ['front_image_quality_score.overall_score']),
            'back_image_quality_score' => self::firstFilledValue($id, ['back_image_quality_score.overall_score']),
        ], fn ($value) => $value !== null && $value !== '');
    }

    private static function firstArrayFromPayload(array $payload, array $paths): array
    {
        foreach ($paths as $path) {
            $value = data_get($payload, $path);
            if (is_array($value)) {
                return $value;
            }
        }

        return [];
    }

    private static function firstFilledValue(array $payload, array $paths): mixed
    {
        foreach ($paths as $path) {
            $value = data_get($payload, $path);
            if (filled($value) && ! is_array($value)) {
                return $value;
            }
        }

        return null;
    }

    private static function normalizeDiditDocumentType(?string $type): ?string
    {
        if (! $type) {
            return null;
        }

        $normalized = strtolower(str_replace(['-', '_'], ' ', $type));

        if (str_contains($normalized, 'driver') || str_contains($normalized, 'license') || str_contains($normalized, 'licence')) {
            return 'drivers_license';
        }

        if (str_contains($normalized, 'national') || str_contains($normalized, 'identity') || str_contains($normalized, 'id card')) {
            return 'philid';
        }

        return 'unknown';
    }

    private static function normalizeDiditDate(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private static function normalizeDiditGender(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        $normalized = strtolower(trim((string) $value));

        return match ($normalized) {
            'm', 'male' => 'M',
            'f', 'female' => 'F',
            default => strtoupper((string) $value),
        };
    }

    private function scoreToPercent(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $score = (float) $value;
        if ($score <= 1) {
            $score *= 100;
        }

        return max(0, min(100, (int) round($score)));
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(VerificationAuditLog::class, 'verification_request_id', '_id')->latest();
    }
}
