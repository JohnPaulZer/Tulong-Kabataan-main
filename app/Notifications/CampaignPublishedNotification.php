<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class CampaignPublishedNotification extends Notification
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
            'type' => 'campaign_published',
            'message' => "Your campaign '{$this->campaign->title}' has been published and is now live!",
            'campaign_id' => $this->campaign->campaign_id,
            'campaign_title' => $this->campaign->title,
            'icon' => 'ri-megaphone-line',
            'url' => route('campaign.view', $this->campaign->campaign_id),
        ];
    }
}
