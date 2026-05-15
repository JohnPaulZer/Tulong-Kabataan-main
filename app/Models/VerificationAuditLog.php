<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Append-only audit log of every decision (automated or manual) made
 * against a VerificationRequest. Stored as a separate collection so it
 * can be retained / exported independently of the request itself.
 */
class VerificationAuditLog extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'verification_audit_logs';

    public const UPDATED_AT = null; // logs are immutable

    protected $fillable = [
        'verification_request_id',
        'user_id',
        'actor_type',   // system | admin
        'actor_id',     // admin id when actor_type=admin, null otherwise
        'action',       // submitted | auto_approved | manual_review | rejected | resubmission_requested | admin_approved | admin_rejected | admin_reupload
        'reason',
        'score',
        'provider_used',
        'metadata',     // arbitrary structured context
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'score' => 'integer',
    ];

    public function verificationRequest()
    {
        return $this->belongsTo(VerificationRequest::class, 'verification_request_id', '_id');
    }
}
