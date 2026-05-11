<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class DropOffPoint extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'drop_off_points';

    protected $fillable = [
        'name',
        'address',
        'schedule_datetime',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
    ];

    public function getDropoffIdAttribute()
    {
        return $this->attributes['_id'] ?? $this->getKey();
    }

    public function donations()
    {
        return $this->hasMany(InKindDonation::class, 'dropoff_id', '_id');
    }
}
