<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VerificationRequest extends Model
{
    use HasFactory;

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
        return $this->attributes['_id'] ?? $this->getKey();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }
}
