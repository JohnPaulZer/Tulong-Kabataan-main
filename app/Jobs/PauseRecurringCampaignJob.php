<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Campaign;
use App\Notifications\RecurringCampaignPausedNotification;

class PauseRecurringCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaignId;

    public function __construct($campaignId)
    {
        $this->campaignId = $campaignId;
    }

    public function handle()
{
    $campaign = Campaign::find($this->campaignId);
    if (!$campaign) return;

    if ($campaign->status === 'active') {
        $campaign->update(['status' => 'paused']);

        $campaign->organizer->notify(new RecurringCampaignPausedNotification($campaign));
    }
}

}
