<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'user_account';
    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

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

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    public function verificationRequests()
    {
        return $this->hasMany(VerificationRequest::class, 'user_id', 'user_id');
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'App.Models.User.' . $this->user_id;
    }

    public function identityStatus()
    {
        return $this->hasOne(IdentityStatus::class, 'user_id', 'user_id');
    }

    public function donations()
    {
        return $this->hasMany(InKindDonation::class, 'user_id', 'user_id');
    }

    // Add these new relationships
    public function campaignUpdates()
    {
        return $this->hasMany(CampaignUpdate::class, 'user_id', 'user_id');
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'user_id', 'user_id');
    }

    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class, 'user_id', 'user_id');
    }
}
