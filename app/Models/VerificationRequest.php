<?php

namespace App\Models;

use App\Models\Concerns\EncryptsSensitiveAttributes;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VerificationRequest extends Model
{
    use EncryptsSensitiveAttributes, HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'verification_requests';

    protected $fillable = [
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
        'rule_flags',
        'reupload_fields',
        'status',
        'review_notes',
    ];

    protected $casts = [
        'dob' => 'date',
        'id_expiry' => 'date',
        'rule_flags' => 'array',
        'reupload_fields' => 'array',
    ];

    public function getRequestIdAttribute()
    {
        return (string) ($this->attributes['_id'] ?? $this->getKey());
    }

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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }
}
