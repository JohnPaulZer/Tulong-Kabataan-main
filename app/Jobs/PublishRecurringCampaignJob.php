<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Campaign;
use Carbon\Carbon;
use App\Notifications\RecurringCampaignPublishedNotification;

class PublishRecurringCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaignId;
    protected $day;
    protected $time;

    public function __construct($campaignId, $day, $time)
    {
        $this->campaignId = $campaignId;
        $this->day = $day;   // e.g. "mon"
        $this->time = $time; // "10:00"
    }

    public function handle()
    {
        $campaign = Campaign::find($this->campaignId);
        if (!$campaign) return;

        // Only activate if campaign is still scheduled
        if ($campaign->status === 'scheduled' || $campaign->status === 'paused') {
            $campaign->update(['status' => 'active']);

              $campaign->organizer->notify(new RecurringCampaignPublishedNotification($campaign));
            // Schedule pause at end of the same day
            $pauseAt = Carbon::now()->endOfDay();
            PauseRecurringCampaignJob::dispatch($this->campaignId)->delay($pauseAt);
        }

        // Schedule next occurrence (next week same day/time)
        $nextDate = Carbon::parse("next {$this->day}")->setTimeFromTimeString($this->time);
        self::dispatch($this->campaignId, $this->day, $this->time)->delay($nextDate);
    }

}
