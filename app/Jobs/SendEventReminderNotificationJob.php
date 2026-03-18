<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\EventReminderNotification;

class SendEventReminderNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $event;
    public $registration;
    public $minutesBefore;

    public function __construct($user, $event, $registration, $minutesBefore)
    {
        $this->user = $user;
        $this->event = $event;
        $this->registration = $registration;
        $this->minutesBefore = $minutesBefore;
    }

    public function handle()
    {
        // This will only run at the scheduled time (24h, 1h, 15m before event)
        $this->user->notify(new EventReminderNotification(
            $this->event,
            $this->registration,
            $this->minutesBefore
        ));
    }
}