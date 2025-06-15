<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class FarmerVerificationStatusNotification extends Notification
{
    protected $status;
    protected $note;

    public function __construct($status, $note = null)
    {
        $this->status = $status;
        $this->note = $note;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Verification ' . ucfirst($this->status),
            'message' => $this->status === 'approved'
                ? 'Your farmer account has been verified. You can now list products.'
                : 'Your verification was rejected. Reason: ' . $this->note,
            'icon' => $this->status === 'approved' ? 'bi-check-circle-fill' : 'bi-x-circle-fill',
            'type' => 'verification'
        ];
    }
}

