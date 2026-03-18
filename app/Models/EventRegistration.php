<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    // Primary key
    protected $primaryKey = 'registration_id';

    // No timestamps (since you didn’t add created_at/updated_at)
    public $timestamps = false;

    // Table name
    protected $table = 'event_registrations';

    // Mass assignable fields
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


    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function volunteerRoles(): BelongsTo
    {
        return $this->belongsTo(VolunteerRole::class, 'vroles_id', 'vroles_id');
    }
}
