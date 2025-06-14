<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Rating;

class NewRatingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $product;
    public $user;
    public $rating;

    public function __construct($product, $user, $rating)
    {
        $this->product = $product;
        $this->user = $user;
        $this->rating = $rating;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Product Rating Submitted',
            'message' => "<strong>{$this->user->name}</strong> rated <strong>{$this->product->name}</strong> with <strong>{$this->rating}⭐</strong>.",
            'icon' => 'bi-star-fill',
            'product_id' => $this->product->id,
            'rating' => $this->rating,
            'type' => 'rating',
            'link' => route('product.show', $this->product->id),
        ];
    }
}
