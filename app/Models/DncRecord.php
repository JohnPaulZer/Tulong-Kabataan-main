<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class DncRecord extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'dnc_records';

    protected $fillable = [
        'date',
        'assessor',
        'event',
        'province',
        'municipality',
        'barangay',
        'households',
        'individuals',
        'pop_male',
        'pop_female',
        'pop_children',
        'pop_elderly',
        'pop_pwds',
        'houses_full',
        'houses_partial',
        'infrastructure',
        'crop_type',
        'crop_hectares',
        'livestock_type',
        'livestock_number',
        'tools_destroyed',
        'facilities_affected',
        'facilities_notes',
        'needs',
        'needs_other',
        'groups',
        'facilities',
        'skills',
        'initiatives',
        'priority',
        'solutions',
        'top_need_1',
        'top_need_2',
        'top_need_3',
    ];

    protected $casts = [
        'infrastructure' => 'array',
        'needs' => 'array',
        'date' => 'date',
        'households' => 'integer',
        'individuals' => 'integer',
    ];

    public function getDncIdAttribute()
    {
        return $this->attributes['_id'] ?? $this->getKey();
    }
}
