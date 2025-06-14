<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusUpdateNotification extends Notification
{
    use Queueable;

    protected $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $status = $this->details['status'];
        $product = $this->details['product'];

        $statusMessages = [
            'To Ship'   => "📦 Your order for <strong>$product</strong> is being prepared to ship.",
            'To Receive'=> "🚚 Your order for <strong>$product</strong> is on the way!",
            'Completed' => "✅ Your order for <strong>$product</strong> has been delivered.",
            'Cancelled' => "❌ Your order for <strong>$product</strong> has been cancelled.",
            'Paid'      => "💰 Payment received for your order of <strong>$product</strong>.",
            'Pending'   => "⏳ Your order for <strong>$product</strong> is awaiting Pending.",
        ];

        return [
            'message' => $statusMessages[$status] ?? "🔔 Your order for <strong>$product</strong> is now <strong>$status</strong>."
        ];
    }
}
