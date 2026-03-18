<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpactReport extends Model
{
    use HasFactory;

    protected $table = 'impact_reports';
    protected $primaryKey = 'impact_report_id';

    protected $fillable = [
        'title',
        'description',
        'report_date',
        'photos'
    ];

    protected $casts = [
        'photos' => 'array',
        'report_date' => 'date'
    ];

    // Relationship with InKindDonations through pivot table
    public function donations()
    {
        return $this->belongsToMany(
            InKindDonation::class,
            'impact_report_donation', // pivot table name
            'impact_report_id',       // foreign key on pivot table
            'inkind_id',              // related key on pivot table
            'impact_report_id',       // local key
            'inkind_id'               // related key
        );
    }
}
