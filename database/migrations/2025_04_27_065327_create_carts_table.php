<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('buyer_id'); // Who owns the cart item
            $table->unsignedBigInteger('product_id'); // What product
            $table->integer('quantity')->default(1); // How many
            $table->timestamps();

            // Foreign keys (optional for now)
            // $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
