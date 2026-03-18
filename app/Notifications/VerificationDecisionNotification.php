<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VerificationDecisionNotification extends Notification
{
    use Queueable;

    public $verificationRequest;
    public $decision;
    public $notes;

    public function __construct($verificationRequest, $decision, $notes = null)
    {
        $this->verificationRequest = $verificationRequest;
        $this->decision = $decision;
        $this->notes = $notes;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'verification_decision',
            'title' => "Account Verification Update",
            'message' => $this->getMessage(),
            'verification_request_id' => $this->verificationRequest->request_id,
            'decision' => $this->decision,
            'notes' => $this->notes,
            'icon' => $this->getIcon(),
            'url' => route('submit.verification'),
        ];
    }

    private function getMessage()
    {
        switch ($this->decision) {
            case 'approved':
                return "Your account verification has been approved successfully. Your account is now fully verified and has access to all platform features.";

            case 'rejected':
                return "Your verification application has been declined. Please review our verification guidelines and submit a new application.";

            case 'request_reupload':
                $fields = $this->verificationRequest->reupload_fields ?? [];
                $fieldNames = implode(', ', array_map(function ($field) {
                    return str_replace('_', ' ', ucfirst($field));
                }, $fields));

                return "The following photos need to be reuploaded for verification:  {$fieldNames}.";

            default:
                return "Your verification request has been updated.";
        }
    }

    private function getIcon()
    {
        return match ($this->decision) {
            'approved' => 'ri-checkbox-circle-line',
            'rejected' => 'ri-close-circle-line',
            'request_reupload' => 'ri-refresh-line',
            default => 'ri-information-line'
        };
    }
}
