<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class InKindDonation extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'in_kind_donations';

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

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function getInkindIdAttribute()
    {
        return $this->attributes['_id'] ?? $this->getKey();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }

    public function dropOffPoint()
    {
        return $this->belongsTo(DropOffPoint::class, 'dropoff_id', '_id');
    }

    /**
     * Get impact reports that reference this donation.
     */
    public function impactReports()
    {
        return ImpactReport::where('donation_ids', $this->_id);
    }

    public function isGuestDonation()
    {
        return is_null($this->user_id);
    }
}
