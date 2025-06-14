<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

$factory->define(Rating::class, function (Faker\Generator $faker) {
    return [
        'order_id' => Order::inRandomOrder()->first()->id,
        'product_id' => Product::inRandomOrder()->first()->id,
        'user_id' => User::where('role', 'buyer')->inRandomOrder()->first()->id,
        'rating' => $faker->numberBetween(1, 5),
        'comment' => $faker->optional()->sentence,
    ];
});
