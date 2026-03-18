<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InKindDonation extends Model
{
    use HasFactory;

    protected $table = 'in_kind_donations';
    protected $primaryKey = 'inkind_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'donor_name',
        'donor_email',
        'donor_phone',
        'dropoff_id',
        'item_name',
        'category',
        'description',
        'quantity',
        'status',
    ];

    // relation to User (if registered donor)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // relation to DropOffPoint
    public function dropOffPoint()
    {
        return $this->belongsTo(DropOffPoint::class, 'dropoff_id', 'dropoff_id');
    }

    public function impactReports()
    {
        return $this->belongsToMany(
            ImpactReport::class,
            'impact_report_donation', // pivot table name
            'inkind_id',              // foreign key on pivot table
            'impact_report_id',       // related key on pivot table
            'inkind_id',              // local key
            'impact_report_id'        // related key
        );
    }

    // Helper: check if donation was made by a guest
    public function isGuestDonation()
    {
        return is_null($this->user_id);
    }
}
