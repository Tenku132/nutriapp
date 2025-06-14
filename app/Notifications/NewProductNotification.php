<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewProductNotification extends Notification
{
    use Queueable;

    public $productName;

    public function __construct($productName)
    {
        $this->productName = $productName;
    }

    public function via($notifiable)
    {
        return ['database']; // store in DB only
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'A new product "' . $this->productName . '" was added.',
            'type' => 'product',
        ];
    }
}

