<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use MongoDB\Laravel\Auth\User as Authenticatable;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'phone_number',
        'birthday',
        'password',
        'google_id',
        'profile_photo_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    public function verificationRequests()
    {
        return $this->hasMany(VerificationRequest::class, 'user_id', '_id');
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'App.Models.User.' . $this->_id;
    }

    public function identityStatus()
    {
        return $this->hasOne(IdentityStatus::class, 'user_id', '_id');
    }

    public function donations()
    {
        return $this->hasMany(InKindDonation::class, 'user_id', '_id');
    }

    public function campaignUpdates()
    {
        return $this->hasMany(CampaignUpdate::class, 'user_id', '_id');
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'user_id', '_id');
    }

    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class, 'user_id', '_id');
    }

    /**
     * Override the default notifications relationship to use our MongoDB
     * DatabaseNotification model instead of the SQL-based one.
     */
    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')
            ->latest();
    }

    /**
     * Backward-compatible accessor: code that references $user->user_id
     * will still work by returning the MongoDB _id.
     */
    public function getUserIdAttribute()
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
}
