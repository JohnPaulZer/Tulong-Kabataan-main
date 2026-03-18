<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $primaryKey = 'donation_id'; // since we used bigIncrements
    protected $table = 'donations';

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

    // 🔗 Relationships
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'campaign_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

}
