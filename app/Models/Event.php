<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Event extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'events';

    protected $fillable = [
        'title',
        'description',
        'photo',
        'start_date',
        'end_date',
        'location',
        'lat',
        'lng',
        'deadline',
        'coordinator_name',
        'coordinator_email',
        'coordinator_phone',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'deadline' => 'datetime',
    ];

    public function getEventIdAttribute()
    {
        return $this->attributes['_id'] ?? $this->getKey();
    }

    public function volunteerRoles()
    {
        return $this->hasMany(VolunteerRole::class, 'event_id', '_id');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class, 'event_id', '_id');
    }
}
