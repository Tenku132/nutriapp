<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminAlertNotification extends Notification
{
    use Queueable;

    protected string $message;
    protected string $icon;
    protected string $link;
    protected ?string $type;
    protected array $extra;

    public function __construct(array $data)
    {
        $this->message = $data['message'] ?? 'New notification';
        $this->icon    = $data['icon'] ?? 'bi-info-circle';
        $this->link    = $data['link'] ?? '#';
        $this->type    = $data['type'] ?? null;
        $this->extra   = $data['extra'] ?? [];
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

public function toDatabase($notifiable)
{
    return array_merge([
        'message' => $this->message,
        'icon'    => $this->icon,
        'link'    => $this->link,
        'type'    => $this->type,
    ], $this->extra);
}

}
