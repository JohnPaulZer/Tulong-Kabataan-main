<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualDonationRequest extends Model
{
    use HasFactory;

    protected $table = 'manual_donation_requests';
    protected $primaryKey = 'request_id';

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


    // Link to the campaign
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'campaign_id');
    }

    // The creator who submitted the request
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
