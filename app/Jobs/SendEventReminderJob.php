<?php

namespace App\Jobs;

use App\Models\EventRegistration;
use App\Mail\EventReminderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEventReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $registration;

    public function __construct(EventRegistration $registration)
    {
        $this->registration = $registration;
    }

    public function handle()
    {
        // Reload relations to be safe
        $this->registration->loadMissing('user', 'event');

        if (!$this->registration->user || !$this->registration->event) {
            return;
        }

        if (!$this->registration->remind_me) {
            return;
        }

        Mail::to($this->registration->user->email)
            ->send(new EventReminderMail($this->registration->user, $this->registration->event, $this->registration));
    }
}
