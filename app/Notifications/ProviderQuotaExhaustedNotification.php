<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Sent to admin accounts when the primary ID verification provider's
 * free-tier monthly quota is exhausted and the system has automatically
 * switched to the fallback provider (manual review mode).
 *
 * This fires ONCE per calendar month per provider exhaustion event.
 */
class ProviderQuotaExhaustedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $exhaustedProvider,
        private string $fallbackProvider,
        private array $usage = [],
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $monthlyUsed  = $this->usage['monthly_used'] ?? '?';
        $monthlyLimit = $this->usage['monthly_limit'] ?? '?';

        return [
            'type'    => 'provider_quota_exhausted',
            'title'   => 'ID Verification: Free Quota Exhausted',
            'message' => "{$this->exhaustedProvider} has used {$monthlyUsed}/{$monthlyLimit} "
                . "verifications this month. The system has automatically switched to "
                . "{$this->fallbackProvider} (manual review mode) for the rest of the month. "
                . "New submissions will still be scored and queued for your review, but "
                . "auto-approval is paused until the quota resets.",
            'icon'    => 'ri-alert-line',
            'url'     => route('account.page'),
            'metadata' => [
                'exhausted_provider' => $this->exhaustedProvider,
                'fallback_provider'  => $this->fallbackProvider,
                'usage'              => $this->usage,
            ],
        ];
    }
}
