<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class RecurringCampaignPausedNotification extends Notification
{
    public $campaign;

    public function __construct($campaign)
    {
        $this->campaign = $campaign;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'recurring_campaign_paused',
            'message' => "Your recurring campaign '{$this->campaign->title}' has been automatically paused and will resume next week.",
            'campaign_id' => $this->campaign->campaign_id,
            'campaign_title' => $this->campaign->title,
            'icon' => 'ri-pause-circle-line',
            'url' => route('campaign.view', $this->campaign->campaign_id),
        ];
    }
}
