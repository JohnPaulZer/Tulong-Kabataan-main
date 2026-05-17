<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Services\Auth\EmailVerificationTokenService;

class CustomVerifyEmail extends Notification
{
    public function __construct(private readonly string $token)
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    protected function verificationUrl($notifiable)
    {
        return route(
            'verification.verify',
            [
                'id' => $notifiable->getKey(),
                'token' => $this->token,
            ]
        );
    }

    public function toMail($notifiable)
    {
        $verifyUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Email | Tulong Kabataan')
            ->view('emails.verify_email', [
                'user' => $notifiable,
                'verifyUrl' => $verifyUrl,
                'expiresInMinutes' => app(EmailVerificationTokenService::class)->expiresInMinutes(),
            ]);
    }
}
