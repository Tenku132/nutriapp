<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rating;

class RatingSeeder extends Seeder
{
    public function run()
    {
        // Make 30 fake ratings
        Rating::factory()->count(30)->create();
    }
}
