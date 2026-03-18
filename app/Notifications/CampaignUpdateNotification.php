<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Models\Campaign;
use App\Models\CampaignUpdate;

class CampaignUpdateNotification extends Notification
{
    public $campaign;
    public $update;

    public function __construct(Campaign $campaign, CampaignUpdate $update)
    {
        $this->campaign = $campaign;
        $this->update = $update;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'campaign_update',
            'message' => "There's a new update for the campaign '{$this->campaign->title}' you donated to!",
            'campaign_id' => $this->campaign->campaign_id,
            'campaign_title' => $this->campaign->title,
            'update_id' => $this->update->update_id,
            'update_message' => $this->update->message,
            'icon' => 'ri-megaphone-line',
            'url' => route('campaign.view', $this->campaign->campaign_id),
        ];
    }
}
