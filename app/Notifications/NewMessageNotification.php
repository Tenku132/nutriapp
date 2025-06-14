<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    protected array $details;

    public function __construct(array $details)
    {
        $this->details = $details;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "<i class='bi bi-chat-dots'></i> You have a new message from <strong>{$this->details['sender']}</strong>",
            'type' => 'message',
            'link' => route('farmer.messages.show', $this->details['sender_id']),
        ];
    }
}
