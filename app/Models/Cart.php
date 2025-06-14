<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'product_id',
        'quantity',
    ];

    // Relationship to the Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship to the User (as buyer)
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
