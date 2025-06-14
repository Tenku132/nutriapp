<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Rating;

class ProductRatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $rating;

    public function __construct(Rating $rating)
    {
        $this->rating = $rating;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $productName = $this->rating->product->name ?? 'Unknown Product';
        $buyerName = $this->rating->buyer->name ?? 'Unknown Buyer';

        return [
            'title' => 'New Product Rating',
            'message' => "Your product <strong>{$productName}</strong> received a <strong>{$this->rating->rating}⭐</strong> rating from <strong>{$buyerName}</strong>.",
            'link' => route('farmer.products.edit', $this->rating->product_id),
            'icon' => 'bi-star-fill',
            'type' => 'farmer_rating',
        ];
    }
}
