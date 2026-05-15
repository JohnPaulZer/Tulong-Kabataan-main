<?php

namespace App\Services\Verification;

use App\Models\VerificationAuditLog;
use App\Models\VerificationRequest;
use Illuminate\Support\Facades\Log;

/**
 * Append-only audit logger for the verification pipeline. Every state
 * transition (auto-approval, manual queue placement, rejection, admin
 * override) flows through this service so we get a full forensic trail
 * inside the verification_audit_logs collection.
 */
class VerificationAuditService
{
    public function record(
        VerificationRequest $request,
        string $action,
        ?string $reason = null,
        array $metadata = [],
        string $actorType = 'system',
        ?string $actorId = null,
    ): void {
        try {
            VerificationAuditLog::create([
                'verification_request_id' => (string) $request->getKey(),
                'user_id'      => (string) $request->user_id,
                'actor_type'   => $actorType,
                'actor_id'     => $actorId,
                'action'       => $action,
                'reason'       => $reason,
                'score'        => $request->confidence_score ?? null,
                'provider_used' => $request->provider_used ?? null,
                'metadata'     => $metadata,
                'ip_address'   => request()?->ip(),
                'user_agent'   => substr((string) request()?->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            // Audit failures must never break the user flow.
            Log::warning('[IdVerification] Failed to write audit log', [
                'error' => $e::class,
                'action' => $action,
            ]);
        }
    }
}
