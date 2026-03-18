<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\ManualDonationRequest;

class ManualDonationStatusNotification extends Notification
{
    use Queueable;

    public $request;
    public $status; // 'approved' or 'rejected'


    public function __construct(ManualDonationRequest $request, $status, $adminName = null)
    {
        $this->request = $request;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $userName = $this->request->creator->first_name ?? 'User';
        $amount = number_format($this->request->amount, 2);
        $campaignTitle = $this->request->campaign->title ?? 'Campaign';

        if ($this->status === 'approved') {
            $title = 'Donation Approved';
            $message = "Your manual donation of ₱{$amount} to \"{$campaignTitle}\" has been approved.";
            $fullMessage = "Great news! Your manual donation of ₱{$amount} to \"{$campaignTitle}\" has been approved by the admin. The funds have been added to the campaign.";
            $icon = 'ri-checkbox-circle-line';
            $color = 'success';
        } else {
            $title = 'Donation Rejected';
            $message = "Your manual donation of ₱{$amount} to \"{$campaignTitle}\" was rejected.";
            $fullMessage = "Your manual donation of ₱{$amount} to \"{$campaignTitle}\" has been rejected by the admin.";
            $icon = 'ri-close-circle-line';
            $color = 'danger';
        }

        // Truncate for dropdown display
        $truncatedMessage = strlen($message) > 70 ? substr($message, 0, 70) . '...' : $message;

        return [
            'type' => 'manual_donation_status',
            'title' => $title,
            'message' => $truncatedMessage,
            'full_message' => $fullMessage,
            'status' => $this->status,
            'campaign_id' => $this->request->campaign_id,
            'campaign_title' => $campaignTitle,
            'request_id' => $this->request->request_id,
            'amount' => $this->request->amount,
            'reference_number' => $this->request->reference_number,
            'icon' => $icon,
            'color' => $color,
            'user_id' => $this->request->user_id,
            'reviewed_at' => now()->toDateTimeString(),
            'url' => route('campaign.view', $this->request->campaign_id),
        ];
    }
}
