<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Campaign;
use Carbon\Carbon;
use App\Notifications\CampaignPublishedNotification;

class PublishCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaignId;
    public $timeout = 60;

    public function __construct($campaignId)
    {
        $this->campaignId = $campaignId;
    }

    public function handle(): void
{
    $campaign = Campaign::where('campaign_id', $this->campaignId)->first();

    if (!$campaign || $campaign->status !== 'scheduled') {
        return;
    }

    // For scheduled campaigns, just activate them
    $campaign->update(['status' => 'active']);

    // Send notification to campaign organizer
    $campaign->organizer->notify(new CampaignPublishedNotification($campaign));
}


}
