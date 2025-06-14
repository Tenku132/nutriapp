<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class FarmerOrderPlacedNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }
public function toArray($notifiable)
{
    return [
        'title' => 'New Order Received',
        'body'  => 'You have a new order for <strong>' . $this->order->product->name . '</strong> from <strong>' . $this->order->buyer->name . '</strong>.',
        'type'  => 'farmer_order' // ✅ Add this line
    ];
}
}
