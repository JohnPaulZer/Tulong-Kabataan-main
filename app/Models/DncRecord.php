<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DncRecord extends Model
{
    use HasFactory;

    protected $table = 'dnc_records';
    protected $primaryKey = 'dnc_id';

    // Mass assignable fields (match migration + form exactly)
    protected $fillable = [
        // A. General Information
        'date',
        'assessor',
        'event',
        'province',
        'municipality',
        'barangay',
        'households',
        'individuals',

        // Population Breakdown
        'pop_male',
        'pop_female',
        'pop_children',
        'pop_elderly',
        'pop_pwds',

        // B. Damage
        'houses_full',
        'houses_partial',
        'infrastructure',

        // Livelihood
        'crop_type',
        'crop_hectares',
        'livestock_type',
        'livestock_number',
        'tools_destroyed',

        // Community Facilities
        'facilities_affected',
        'facilities_notes',

        // C. Needs
        'needs',
        'needs_other',

        // D. Capacities
        'groups',
        'facilities',
        'skills',
        'initiatives',

        // E. Prioritization
        'priority',
        'solutions',
        'top_need_1',
        'top_need_2',
        'top_need_3',
    ];

    // Cast JSON and array fields automatically
    protected $casts = [
        'infrastructure' => 'array',
        'needs' => 'array',
        'date' => 'date',
    ];
}
