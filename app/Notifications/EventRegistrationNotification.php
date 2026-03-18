<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class EventRegistrationNotification extends Notification
{
    public $event;
    public $registration;

    public function __construct($event, $registration)
    {
        $this->event = $event;
        $this->registration = $registration;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $roleText = $this->registration->registered_role ?
            " as {$this->registration->registered_role}" : '';

        return [
            'type' => 'event_registration',
            'message' => "You have successfully registered for '{$this->event->title}'{$roleText}",
            'event_id' => $this->event->event_id,
            'event_title' => $this->event->title,
            'registration_id' => $this->registration->registration_id,
            'icon' => 'ri-calendar-event-line',
            'url' => route('event.view', $this->event->event_id),
        ];
    }
}
