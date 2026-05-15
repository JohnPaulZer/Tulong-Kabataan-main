<?php

namespace App\Services\Verification;

use App\Models\SiteSetting;
use App\Services\Verification\Contracts\VerificationProvider;
use App\Services\Verification\Providers\DiditProvider;
use App\Services\Verification\Providers\GoogleVisionProvider;
use App\Services\Verification\Providers\NullVerificationProvider;
use App\Services\Verification\Providers\OcrSpaceProvider;

/**
 * Provider resolver. Checks (in order):
 *   1. Explicit $key argument (used by fallback logic)
 *   2. SiteSetting 'verification.provider' (admin panel override)
 *   3. config('id_verification.provider') (.env default)
 *
 * If the resolved provider has no API key, falls back to the
 * NullVerificationProvider so the pipeline degrades to "manual review"
 * instead of crashing.
 */
class OcrService
{
    public function __construct(
        private DiditProvider $didit,
        private OcrSpaceProvider $ocrSpace,
        private GoogleVisionProvider $googleVision,
        private NullVerificationProvider $null,
    ) {
    }

    public function provider(?string $key = null): VerificationProvider
    {
        if (! $key) {
            // Admin panel override takes priority over .env
            try {
                $key = SiteSetting::get('verification.provider');
            } catch (\Throwable) {
                // DB not available yet (migrations, etc.) — skip
            }
        }

        $key = $key ?: (string) config('id_verification.provider', 'didit');

        $candidate = match (strtolower($key)) {
            'didit'         => $this->didit,
            'ocr_space',
            'ocrspace'      => $this->ocrSpace,
            'google_vision',
            'googlevision'  => $this->googleVision,
            default         => $this->null,
        };

        return $candidate->isConfigured() ? $candidate : $this->null;
    }
}
