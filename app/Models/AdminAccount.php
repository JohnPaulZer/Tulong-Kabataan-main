<?php

namespace App\Models;

use App\Models\Concerns\EncryptsSensitiveAttributes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use MongoDB\Laravel\Eloquent\Model;

class AdminAccount extends Model
{
    use EncryptsSensitiveAttributes, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'admin_accounts';

    protected $fillable = [
        'username',
        'email',
        'password',
        'reset_token',
        'reset_token_expiry',
    ];

    protected $hidden = [
        'password',
        'reset_token',
    ];

    protected $casts = [
        'reset_token_expiry' => 'datetime',
    ];

    public function getAdminIdAttribute()
    {
        return (string) ($this->attributes['_id'] ?? $this->getKey());
    }

    public function setPasswordAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['password'] = $value;
            return;
        }

        $this->attributes['password'] = password_get_info((string) $value)['algo'] !== 0
            ? (string) $value
            : Hash::make((string) $value);
    }

    public function getResetTokenAttribute($value)
    {
        return $this->decryptSensitiveValue($value);
    }

    public function setResetTokenAttribute($value): void
    {
        $this->attributes['reset_token'] = $this->encryptSensitiveValue($value);
    }

    /**
     * Override the default notifications relationship to use the MongoDB
     * DatabaseNotification model.
     */
    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')
            ->latest();
    }
}
