<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class DonationDistributedNotification extends Notification
{
    public $donations;
    public $impactReport;
    public $itemCount;
    public $totalQuantity;

    public function __construct($donations, $impactReport)
    {
        $this->donations = $donations instanceof Collection ? $donations : collect([$donations]);
        $this->impactReport = $impactReport;
        $this->itemCount = $this->donations->count();
        $this->totalQuantity = $this->donations->sum('quantity');
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $items = $this->donations->pluck('item_name')->toArray();

        if ($this->itemCount === 1) {
            $message = "Your donation of '{$items[0]}' has been distributed!";
        } elseif ($this->itemCount === 2) {
            $message = "Your donations of '{$items[0]}' and '{$items[1]}' have been distributed!";
        } elseif ($this->itemCount === 3) {
            $message = "Your donations of '{$items[0]}', '{$items[1]}', and '{$items[2]}' have been distributed!";
        } else {
            $firstTwo = array_slice($items, 0, 2);
            $remainingCount = $this->itemCount - 2;
            $message = "Your donations of '" . implode("', '", $firstTwo) . "' and {$remainingCount} more items have been distributed!";
        }

        return [
            'type' => 'donation_distributed',
            'message' => $message,
            'impact_report_id' => $this->impactReport->impact_report_id,
            'impact_report_title' => $this->impactReport->title,
            'donation_ids' => $this->donations->pluck('inkind_id')->toArray(),
            'item_count' => $this->itemCount,
            'total_quantity' => $this->totalQuantity,
            'items' => $items,
            'icon' => 'ri-gift-line',
            'url' => route('inkind.tracking', $this->impactReport->impact_report_id),
        ];
    }
}
