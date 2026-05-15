<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Crypt;
use Throwable;

trait EncryptsSensitiveAttributes
{
    protected function encryptSensitiveValue(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $value = (string) $value;

        if (str_starts_with($value, 'enc:')) {
            return $value;
        }

        return 'enc:' . Crypt::encryptString($value);
    }

    protected function decryptSensitiveValue(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $value = (string) $value;

        if (! str_starts_with($value, 'enc:')) {
            return $value;
        }

        try {
            return Crypt::decryptString(substr($value, 4));
        } catch (Throwable) {
            return null;
        }
    }
}
