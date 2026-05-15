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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(VerificationAuditLog::class, 'verification_request_id', '_id')->latest();
    }
}
