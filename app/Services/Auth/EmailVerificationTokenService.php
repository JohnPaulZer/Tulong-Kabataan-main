<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class EmailVerificationTokenService
{
    public function issue(User $user): string
    {
        $plainToken = Str::random(64);
        $now = now();

        $user->forceFill([
            'email_verification_token_hash' => $this->hash($plainToken),
            'email_verification_token_expires_at' => $now->copy()->addMinutes($this->expiresInMinutes()),
            'email_verification_token_used_at' => null,
            'email_verification_sent_at' => $now,
        ])->save();

        return $plainToken;
    }

    public function verify(string $userId, string $plainToken): array
    {
        if (strlen($plainToken) < 40) {
            return $this->result('invalid', 'This verification link is invalid. Please request a new one.');
        }

        $user = User::find($userId);

        if (! $user) {
            return $this->result('invalid', 'This verification link is invalid. Please request a new one.');
        }

        $storedHash = (string) ($user->email_verification_token_hash ?? '');
        if ($storedHash === '' || ! hash_equals($storedHash, $this->hash($plainToken))) {
            return $this->result('invalid', 'This verification link is invalid. Please request a new one.', $user);
        }

        if ($user->email_verification_token_used_at || $user->hasVerifiedEmail()) {
            return $this->result('already_used', 'This verification link has already been used.', $user);
        }

        $expiresAt = $this->asCarbon($user->email_verification_token_expires_at ?? null);
        if (! $expiresAt || $expiresAt->isPast()) {
            return $this->result('expired', 'This verification link has expired. Please request a new one.', $user);
        }

        $now = now();
        $user->forceFill([
            'email_verified_at' => $now,
            'email_verification_token_used_at' => $now,
            'status' => ($user->status ?? 'active') === 'suspended' ? 'suspended' : 'active',
        ])->save();

        return $this->result('success', 'Your email has been verified successfully.', $user);
    }

    public function hash(string $plainToken): string
    {
        return hash_hmac('sha256', $plainToken, (string) config('app.key'));
    }

    public function expiresInMinutes(): int
    {
        return max(1, (int) config('auth.verification.expire', 60));
    }

    private function asCarbon(mixed $value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function result(string $status, string $message, ?User $user = null): array
    {
        return [
            'status' => $status,
            'message' => $message,
            'user' => $user,
        ];
    }
}
