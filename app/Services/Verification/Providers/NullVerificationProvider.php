<?php

namespace App\Services\Verification\Providers;

use App\Services\Verification\Contracts\VerificationProvider;

/**
 * Used when ID_VERIFICATION_PROVIDER=none or when the configured provider
 * is missing credentials. Never auto-decides — always routes to manual
 * review with a clear "no provider configured" reason.
 */
class NullVerificationProvider implements VerificationProvider
{
    public function name(): string
    {
        return 'none';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function supportsAuthenticity(): bool
    {
        return false;
    }

    public function verify(string $frontAbsolutePath, ?string $backAbsolutePath = null, array $context = []): array
    {
        return [
            'success'      => false,
            'provider'     => $this->name(),
            'reference_id' => null,
            'raw_text'     => '',
            'extracted'    => [],
            'authenticity' => [],
            'raw'          => null,
            'error'        => 'No verification provider configured',
        ];
    }
}
