<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropOffPoint extends Model
{
    use HasFactory;

    protected $table = 'drop_off_points';
    protected $primaryKey = 'dropoff_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'address',
        'schedule_datetime',
        'latitude',
        'longitude',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function donations()
    {
        return $this->hasMany(InKindDonation::class, 'dropoff_id', 'dropoff_id');
    }
}
