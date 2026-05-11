<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class VolunteerRole extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'volunteer_roles';

    protected $fillable = ['event_id', 'name', 'description'];

    public function getVrolesIdAttribute()
    {
        return (string) ($this->attributes['_id'] ?? $this->getKey());
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', '_id');
    }
}
