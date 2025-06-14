<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'buyer_id')) {
                $table->unsignedBigInteger('buyer_id')->after('id');

                // Optional FK constraint:
                // $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'buyer_id')) {
                $table->dropColumn('buyer_id');
            }
        });
    }
};
