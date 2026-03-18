<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CampaignUpdate extends Model
{
    use HasFactory;

    protected $table = 'campaign_updates';
    protected $primaryKey = 'update_id';
    public $incrementing = true;
    protected $keyType = 'int';

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

    /**
     * Relationships
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'campaign_id');
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}