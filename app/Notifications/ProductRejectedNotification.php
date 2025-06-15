<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProductRejectedNotification extends Notification
{
    use Queueable;

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Product Rejected ❌',
            'message' => "Your product <strong>{$this->product->name}</strong> was rejected. Please review and resubmit if needed.",
            'link' => route('farmer.products.index'),
            'icon' => 'bi-x-circle-fill',
        ];
    }
}
