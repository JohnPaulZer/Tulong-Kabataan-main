<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class CampaignView extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'campaign_views';

    protected $fillable = [
        'campaign_id',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', '_id');
    }
}
