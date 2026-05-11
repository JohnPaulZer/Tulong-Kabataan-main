<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class ManualDonationRequest extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'manual_donation_requests';

    protected $fillable = [
        'campaign_id',
        'user_id',
        'amount',
        'reference_number',
        'proof_image',
        'status',
        'approved_by',
        'reviewed_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'reviewed_at' => 'datetime',
    ];

    public function getRequestIdAttribute()
    {
        return (string) ($this->attributes['_id'] ?? $this->getKey());
    }

    public function setAmountAttribute($value): void
    {
        $this->attributes['amount'] = is_numeric($value) ? (float) $value : 0.0;
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', '_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }
}
