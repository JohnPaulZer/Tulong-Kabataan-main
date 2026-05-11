<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CampaignUpdate extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'campaign_updates';

    protected $fillable = [
        'campaign_id',
        'user_id',
        'message',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getUpdateIdAttribute()
    {
        return (string) ($this->attributes['_id'] ?? $this->getKey());
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', '_id');
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }
}
