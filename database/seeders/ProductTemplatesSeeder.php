<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTemplatesSeeder extends Seeder
{
    public function run()
    {
        DB::table('product_templates')->insert([
            // Fruits
            ['category' => 'Fruits', 'name' => 'Mango', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Fruits', 'name' => 'Banana', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Fruits', 'name' => 'Calamansi', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Fruits', 'name' => 'Pineapple', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Fruits', 'name' => 'Papaya', 'created_at' => now(), 'updated_at' => now()],

            // Vegetables
            ['category' => 'Vegetable', 'name' => 'Eggplant', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Vegetable', 'name' => 'Tomato', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Vegetable', 'name' => 'Squash', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Vegetable', 'name' => 'String Beans', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Vegetable', 'name' => 'Bitter Gourd', 'created_at' => now(), 'updated_at' => now()],

            // Grains
            ['category' => 'Grains', 'name' => 'Rice', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Grains', 'name' => 'Corn', 'created_at' => now(), 'updated_at' => now()],

            // Spices
            ['category' => 'Spices', 'name' => 'Garlic', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Spices', 'name' => 'Onion', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Spices', 'name' => 'Ginger', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Spices', 'name' => 'Chili Pepper', 'created_at' => now(), 'updated_at' => now()],

            // Beverages
            ['category' => 'Beverages', 'name' => 'Coffee Beans', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Beverages', 'name' => 'Cacao', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}


