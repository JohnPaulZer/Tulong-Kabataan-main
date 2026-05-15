<?php

namespace App\Services\Verification;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Defends our free-tier API quotas from being exhausted.
 *
 * The pipeline already enforces a per-user daily attempt limit (in
 * IdVerificationService::validateUpload). That stops a single attacker.
 * It does NOT stop the cumulative load from many legitimate users
 * pushing us past the provider's free tier (e.g. OCR.Space 25k/month).
 *
 * QuotaGuard tracks two counters per provider, both stored in the
 * application cache:
 *
 *   - daily counter   -> resets at local midnight
 *   - monthly counter -> resets on the 1st of each month
 *
 * Limits are configured per provider in config/id_verification.php and
 * can be tuned without redeploying. When the cap is reached, the guard
 * returns false; the orchestrator then routes the submission to the
 * admin queue and logs the event so we can see when we're approaching
 * (or hitting) the cap.
 *
 * The counters are advisory — they protect free-tier quotas, they do
 * NOT protect against simultaneous bursts from concurrent requests.
 * The per-user daily cap + throttle:upload handle that side already.
 */
class QuotaGuard
{
    public function reserve(string $provider): bool
    {
        $cfg = $this->limitsFor($provider);
        if ($cfg === null) {
            return true; // no limits configured — allow
        }

        $dayKey   = $this->cacheKey($provider, 'day', now()->format('Ymd'));
        $monthKey = $this->cacheKey($provider, 'month', now()->format('Ym'));

        $dayCount   = (int) Cache::get($dayKey, 0);
        $monthCount = (int) Cache::get($monthKey, 0);

        if ($cfg['daily'] > 0 && $dayCount >= $cfg['daily']) {
            $this->logExhausted($provider, 'daily', $dayCount, $cfg['daily']);
            return false;
        }
        if ($cfg['monthly'] > 0 && $monthCount >= $cfg['monthly']) {
            $this->logExhausted($provider, 'monthly', $monthCount, $cfg['monthly']);
            return false;
        }

        // Reserve the slot. Cache::increment is atomic under the redis/file
        // store; for the file driver it's still safe because we accept some
        // inaccuracy (we leave headroom in the limit).
        Cache::put($dayKey, $dayCount + 1, now()->endOfDay());
        Cache::put($monthKey, $monthCount + 1, now()->endOfMonth());

        return true;
    }

    /**
     * Roll back a reservation if the call ended up not being made (e.g. the
     * provider failed the network call before consuming a quota unit).
     * Best-effort — slight over-counting is fine, we'd rather not let a
     * request that genuinely consumed quota get refunded.
     */
    public function refund(string $provider): void
    {
        $cfg = $this->limitsFor($provider);
        if ($cfg === null) {
            return;
        }
        $dayKey   = $this->cacheKey($provider, 'day', now()->format('Ymd'));
        $monthKey = $this->cacheKey($provider, 'month', now()->format('Ym'));
        Cache::put($dayKey, max(0, ((int) Cache::get($dayKey, 0)) - 1), now()->endOfDay());
        Cache::put($monthKey, max(0, ((int) Cache::get($monthKey, 0)) - 1), now()->endOfMonth());
    }

    /**
     * Current usage snapshot — useful for a status endpoint or admin tile.
     */
    public function usage(string $provider): array
    {
        $cfg = $this->limitsFor($provider) ?? ['daily' => 0, 'monthly' => 0];
        return [
            'provider'      => $provider,
            'daily_used'    => (int) Cache::get($this->cacheKey($provider, 'day', now()->format('Ymd')), 0),
            'daily_limit'   => $cfg['daily'],
            'monthly_used'  => (int) Cache::get($this->cacheKey($provider, 'month', now()->format('Ym')), 0),
            'monthly_limit' => $cfg['monthly'],
        ];
    }

    private function limitsFor(string $provider): ?array
    {
        $providers = (array) config('id_verification.quotas', []);
        if (! isset($providers[$provider])) {
            return null;
        }
        $cfg = (array) $providers[$provider];
        return [
            'daily'   => max(0, (int) ($cfg['daily']   ?? 0)),
            'monthly' => max(0, (int) ($cfg['monthly'] ?? 0)),
        ];
    }

    private function cacheKey(string $provider, string $window, string $bucket): string
    {
        return "id_verify_quota:{$provider}:{$window}:{$bucket}";
    }

    private function logExhausted(string $provider, string $window, int $used, int $limit): void
    {
        Log::warning('[IdVerification] Provider quota exhausted', [
            'provider' => $provider,
            'window'   => $window,
            'used'     => $used,
            'limit'    => $limit,
        ]);
    }
}
