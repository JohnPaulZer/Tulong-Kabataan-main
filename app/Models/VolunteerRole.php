<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerRole extends Model
{
    protected $primaryKey = 'vroles_id';
    protected $fillable = ['event_id','name','description'];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}

