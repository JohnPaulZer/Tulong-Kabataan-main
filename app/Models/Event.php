<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $primaryKey = 'event_id'; // since not "id"
    protected $fillable = [
        'title',
        'description',
        'photo',
        'start_date',
        'end_date',
        'location',
        'deadline',
        'coordinator_name',
        'coordinator_email',
        'coordinator_phone'
    ];

    public function volunteerRoles()
    {
        return $this->hasMany(VolunteerRole::class, 'event_id');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class, 'event_id', 'event_id');
    }
}
