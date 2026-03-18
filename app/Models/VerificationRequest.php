<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VerificationRequest extends Model
{
    use HasFactory;

    protected $table = 'verification_requests';
    protected $primaryKey = 'request_id';  
    public $incrementing = true;
    protected $keyType = 'int';

    
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
        'id_expiry',
        'id_front_path',
        'id_back_path',
        'face_photo_path', 
        'selfie_path',
        'rule_flags',
        'status',
        'review_notes'
        
    ];

    protected $casts = [
        'dob'        => 'date',
        'id_expiry'  => 'date',
        'rule_flags' => 'array',
        'reupload_fields' => 'array', 
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }


}
