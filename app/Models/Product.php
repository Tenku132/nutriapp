<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Order;
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'stock',
        'category',
        'province',
        'description',
        'image',
        'farmer_id',
        'ripeness',
        'harvested_at',
        'shelf_life', 
        'storage',
    ];

public function orders()
{
    return $this->hasMany(Order::class)->where('status', 'delivered');
}

    public function getSalesCountAttribute()
    {
        return $this->orders()->count();
    }


public function reviews()
{
    return $this->hasMany(Rating::class);
}
public function farmer()
{
    return $this->belongsTo(User::class, 'farmer_id');
}
public function sales()
{
    return $this->hasMany(Order::class, 'product_id');
}


public function user()
{
   return $this->belongsTo(User::class, 'farmer_id'); // Or 'farmer_id' if that's your foreign key
}

}
