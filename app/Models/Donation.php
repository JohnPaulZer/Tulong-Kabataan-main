<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'donations';

    protected $fillable = [
        'campaign_id',
        'user_id',
        'is_anonymous',
        'donor_name',
        'donor_email',
        'amount',
        'reference_number',
        'proof_image',
        'message_code',
        'status',
    ];

    protected $casts = [
        'amount' => 'float',
        'is_anonymous' => 'boolean',
    ];

    public function getDonationIdAttribute()
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }
}
