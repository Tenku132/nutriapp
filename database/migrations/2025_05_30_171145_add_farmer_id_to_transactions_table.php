<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('transactions', function (Blueprint $table) {
        $table->unsignedBigInteger('farmer_id')->nullable()->after('buyer_id');

        // (Optional) Add foreign key constraint
        $table->foreign('farmer_id')->references('id')->on('users')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('transactions', function (Blueprint $table) {
        $table->dropForeign(['farmer_id']);
        $table->dropColumn('farmer_id');
    });
}

};
