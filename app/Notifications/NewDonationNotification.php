<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewDonationNotification extends Notification
{
    public $donation;
    public $campaign;

    public function __construct($donation, $campaign)
    {
        $this->donation = $donation;
        $this->campaign = $campaign;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $donorName = $this->donation->is_anonymous ? 'Anonymous' : ($this->donation->donor_name ?? 'Someone');
        $campaignTitle = $this->campaign->title;


        $fullMessage = "{$donorName} donated ₱" . number_format($this->donation->amount, 2) . " to {$this->campaign->title}";


        $truncatedMessage = "{$donorName} donated ₱" . number_format($this->donation->amount, 2) . " to {$campaignTitle}";
        if (strlen($truncatedMessage) > 70) {
            $truncatedMessage = substr($truncatedMessage, 0, 70) . '...';
        }

        return [
            'type' => 'new_donation',
            'message' => $truncatedMessage, // For dropdown
            'full_message' => $fullMessage, // For notifications page
            'campaign_id' => $this->campaign->campaign_id,
            'campaign_title' => $this->campaign->title,
            'donation_id' => $this->donation->donation_id,
            'amount' => $this->donation->amount,
            'donor_name' => $donorName,
            'icon' => 'ri-user-heart-line',
            'url' => route('campaign.view', $this->campaign->campaign_id),
            'is_anonymous' => $this->donation->is_anonymous,
        ];
    }
}
