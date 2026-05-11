<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'campaigns';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'campaign_organizer',
        'target_amount',
        'current_amount',
        'status',
        'schedule_type',
        'starts_at',
        'ends_at',
        'recurring_days',
        'recurring_time',
        'images',
        'featured_image',
        'qr_code',
        'gcash_number',
        'views',
        'donor_count',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'recurring_days' => 'array',
        'images' => 'array',
        'allow_anonymous' => 'boolean',
        'target_amount' => 'float',
        'current_amount' => 'float',
        'views' => 'integer',
        'donor_count' => 'integer',
    ];

    /**
     * Backward-compatible accessor for code referencing $campaign->campaign_id
     */
    public function getCampaignIdAttribute()
    {
        return (string) ($this->attributes['_id'] ?? $this->getKey());
    }

    public function adjustDonationStats(float $amountDelta = 0, int $donorDelta = 0): bool
    {
        $currentAmount = (float) ($this->current_amount ?? 0);
        $donorCount = (int) ($this->donor_count ?? 0);

        $this->current_amount = max(0, $currentAmount + $amountDelta);
        $this->donor_count = max(0, $donorCount + $donorDelta);

        return $this->save();
    }

    public function setTargetAmountAttribute($value): void
    {
        $this->attributes['target_amount'] = is_numeric($value) ? (float) $value : 0.0;
    }

    public function setCurrentAmountAttribute($value): void
    {
        $this->attributes['current_amount'] = is_numeric($value) ? (float) $value : 0.0;
    }

    public function setDonorCountAttribute($value): void
    {
        $this->attributes['donor_count'] = is_numeric($value) ? (int) $value : 0;
    }

    public function setViewsAttribute($value): void
    {
        $this->attributes['views'] = is_numeric($value) ? (int) $value : 0;
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }

    public function updates()
    {
        return $this->hasMany(CampaignUpdate::class, 'campaign_id', '_id')
            ->orderBy('created_at', 'desc');
    }

    public function manualRequests()
    {
        return $this->hasMany(ManualDonationRequest::class, 'campaign_id', '_id');
    }

    public function viewsTracked()
    {
        return $this->hasMany(CampaignView::class, 'campaign_id', '_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'campaign_id', '_id');
    }
}
