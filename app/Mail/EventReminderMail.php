<?php

// app/Mail/EventReminderMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $user;
    public $registration;
    public function __construct($user, $event, $registration)
    {
        $this->user = $user;
        $this->event = $event;
        $this->registration = $registration;
    }

    public function build()
    {
        // ensure you have MAIL_FROM_ADDRESS set; override here if needed:
        $from = config('mail.from.address') ?? env('MAIL_FROM_ADDRESS');

        $mail = $this->subject('Reminder: Upcoming Event')
            ->view('emails.event_reminder')
            ->with([
                'user' => $this->user,
                'event' => $this->event,
                'registration' => $this->registration,
            ]);

        if ($from) {
            $mail->from($from, config('mail.from.name') ?? env('MAIL_FROM_NAME'));
        }

        return $mail;
    }
}
