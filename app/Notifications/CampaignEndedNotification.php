<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class CampaignEndedNotification extends Notification
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
            'type' => 'campaign_ended',
            'message' => "Your campaign '{$this->campaign->title}' has ended",
            'campaign_id' => $this->campaign->campaign_id,
            'campaign_title' => $this->campaign->title,
            'icon' => 'ri-flag-line',
            'url' => route('campaign.view', $this->campaign->campaign_id),
        ];
    }
}
