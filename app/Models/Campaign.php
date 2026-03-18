<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory;

    protected $table = 'campaigns';
    protected $primaryKey = 'campaign_id';
    public $incrementing = true;
    protected $keyType = 'int';

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


    // Cast fields to appropriate types
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'recurring_days' => 'array',
        'images' => 'array',
        'allow_anonymous' => 'boolean',
        'recurring_time' => 'datetime:H:i',
    ];

    /**
     * Relationships
     */
    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Add this new relationship for updates
    public function updates()
    {
        return $this->hasMany(CampaignUpdate::class, 'campaign_id', 'campaign_id')
            ->orderBy('created_at', 'desc');
    }


    public function manualRequests()
    {
        return $this->hasMany(ManualDonationRequest::class, 'campaign_id', 'campaign_id');
    }
    public function viewsTracked()
    {
        return $this->hasMany(CampaignView::class, 'campaign_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'campaign_id', 'campaign_id');
    }
}
