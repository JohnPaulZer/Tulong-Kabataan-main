<?php

namespace App\Services\Security;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileService
{
    public function enabled(): bool
    {
        return filter_var(config('services.turnstile.enabled', false), FILTER_VALIDATE_BOOLEAN)
            && filled($this->siteKey())
            && filled($this->secretKey());
    }

    public function siteKey(): ?string
    {
        return config('services.turnstile.site_key');
    }

    public function verifyRequest(Request $request, string $expectedAction): bool
    {
        if (! $this->enabled()) {
            return true;
        }

        $token = (string) $request->input('cf-turnstile-response', '');
        if ($token === '') {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout((int) config('services.turnstile.timeout', 8))
                ->post((string) config('services.turnstile.endpoint'), [
                    'secret' => $this->secretKey(),
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);
        } catch (\Throwable $e) {
            Log::warning('Turnstile verification request failed.', [
                'error' => $e::class,
                'action' => $expectedAction,
            ]);

            return false;
        }

        $payload = $response->json() ?: [];
        $success = (bool) ($payload['success'] ?? false);
        $hostname = (string) ($payload['hostname'] ?? '');
        $action = (string) ($payload['action'] ?? '');

        if (! $success) {
            Log::notice('Turnstile token rejected.', [
                'status' => $response->status(),
                'action' => $action ?: $expectedAction,
                'error_codes' => $payload['error-codes'] ?? [],
            ]);

            return false;
        }

        if (! $this->usingTestSecretKey() && $expectedAction !== '' && $action !== '' && ! hash_equals($expectedAction, $action)) {
            Log::notice('Turnstile action mismatch.', [
                'expected_action' => $expectedAction,
                'actual_action' => $action,
            ]);

            return false;
        }

        $allowedHostnames = $this->allowedHostnames();
        if ($allowedHostnames !== [] && ! in_array($hostname, $allowedHostnames, true)) {
            Log::notice('Turnstile hostname mismatch.', [
                'hostname' => $hostname,
                'action' => $action ?: $expectedAction,
            ]);

            return false;
        }

        return true;
    }

    public function failureMessage(): string
    {
        return 'We could not verify the security check. Please complete it and try again.';
    }

    private function secretKey(): ?string
    {
        return config('services.turnstile.secret_key');
    }

    private function usingTestSecretKey(): bool
    {
        return in_array($this->secretKey(), [
            '1x0000000000000000000000000000000AA',
            '2x0000000000000000000000000000000AA',
            '3x0000000000000000000000000000000AA',
        ], true);
    }

    private function allowedHostnames(): array
    {
        $hostnames = (string) config('services.turnstile.allowed_hostnames', '');

        return array_values(array_filter(array_map('trim', explode(',', $hostnames))));
    }
}
