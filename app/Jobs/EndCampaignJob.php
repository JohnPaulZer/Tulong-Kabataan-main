<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Campaign;
use Illuminate\Support\Facades\Log;
use App\Notifications\CampaignEndedNotification;


class EndCampaignJob implements ShouldQueue
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

    if (!$campaign) {
        return;
    }

    // End campaign if it's active OR scheduled (in case publish job failed)
    if (in_array($campaign->status, ['active', 'scheduled'])) {
        $campaign->update(['status' => 'ended']);

         // Send notification to campaign organizer
        $campaign->organizer->notify(new CampaignEndedNotification($campaign));

    }
}
}
