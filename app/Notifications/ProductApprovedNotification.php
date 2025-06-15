<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProductApprovedNotification extends Notification
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
            'title' => 'Product Approved ✅',
            'message' => "Your product <strong>{$this->product->name}</strong> has been approved and is now live on the marketplace.",
            'link' => route('farmer.products.index'),
            'icon' => 'bi-check-circle-fill',
        ];
    }
}
