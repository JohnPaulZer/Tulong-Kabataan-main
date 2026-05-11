<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'event_registrations';

    protected $fillable = [
        'user_id',
        'event_id',
        'vroles_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'messenger_link',
        'age',
        'sex',
        'address',
        'registered_role',
        'remind_me',
        'reminder_minutes',
        'status',
    ];

    protected $casts = [
        'remind_me' => 'boolean',
        'age' => 'integer',
        'reminder_minutes' => 'integer',
    ];

    public function getRegistrationIdAttribute()
    {
        return $this->attributes['_id'] ?? $this->getKey();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', '_id');
    }

    public function volunteerRoles(): BelongsTo
    {
        return $this->belongsTo(VolunteerRole::class, 'vroles_id', '_id');
    }
}
