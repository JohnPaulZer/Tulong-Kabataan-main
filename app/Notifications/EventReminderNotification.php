<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification
{
    public $event;
    public $registration;
    public $minutesBefore;

    public function __construct($event, $registration, $minutesBefore)
    {
        $this->event = $event;
        $this->registration = $registration;
        $this->minutesBefore = $minutesBefore;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $timeText = $this->getTimeText();
        $roleText = $this->registration->registered_role ?
            " as {$this->registration->registered_role}" : '';

        return [
            'type' => 'event_reminder',
            'message' =>  "You're registered for '{$this->event->title}' which starts {$timeText}{$roleText}",
            'event_id' => $this->event->event_id,
            'event_title' => $this->event->title,
            'registration_id' => $this->registration->registration_id,
            'minutes_before' => $this->minutesBefore,
            'icon' => 'ri-alarm-line',
            'url' => route('event.view', $this->event->event_id),
        ];
    }

    private function getTimeText()
    {
        $minutes = (int) $this->minutesBefore;

        if ($minutes >= 1440) {
            $days = (int) floor($minutes / 1440);
            return "in {$days} " . ($days === 1 ? 'day' : 'days');
        } elseif ($minutes >= 60) {
            $hours = (int) floor($minutes / 60);
            return "in {$hours} " . ($hours === 1 ? 'hour' : 'hours');
        } else {
            return "in {$minutes} minutes";
        }
    }
}
