<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class InKindDonationConfirmationNotification extends Notification
{
    public $donations;
    public $dropoffPoint;
    public $totalItems;
    public $itemSummary;

    public function __construct($donations, $dropoffPoint, $totalItems, $itemSummary)
    {
        $this->donations = $donations;
        $this->dropoffPoint = $dropoffPoint;
        $this->totalItems = $totalItems;
        $this->itemSummary = $itemSummary;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $dropoffName = $this->dropoffPoint->name;

        if ($this->totalItems > 1) {
            $message = "Thank you for your {$this->totalItems} in-kind donations! Your items are ready to be received at {$dropoffName}";
        } else {
            $donation = $this->donations[0];
            $message = "Thank you for your in-kind donation! Your {$donation->quantity} {$donation->item_name} is ready to be received at {$dropoffName}";
        }

        return [
            'type' => 'inkind_donation_confirmation',
            'message' => $message,
            'inkind_ids' => collect($this->donations)->pluck('inkind_id')->toArray(),
            'icon' => 'ri-gift-line',
            'status' => 'ready_for_dropoff',
            'dropoff_location' => $dropoffName,
            'total_items' => $this->totalItems,
            'item_summary' => $this->itemSummary
        ];
    }
}
