<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->string('intent_id'); // renamed from payment_intent_id
        $table->string('method')->nullable(); // gcash, maya, card
        $table->integer('amount');
        $table->string('status'); // e.g., paid, failed, pending
        $table->unsignedBigInteger('buyer_id')->nullable(); // renamed from user_id
        $table->timestamps();

        $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
