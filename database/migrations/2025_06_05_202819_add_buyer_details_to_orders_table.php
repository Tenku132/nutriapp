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
    Schema::table('orders', function (Blueprint $table) {
        $table->string('buyer_phone')->nullable();
        $table->string('buyer_address')->nullable();
        $table->string('buyer_city')->nullable();
        $table->string('buyer_region')->nullable();
        $table->string('buyer_postal_code')->nullable();
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn([
            'buyer_phone',
            'buyer_address',
            'buyer_city',
            'buyer_region',
            'buyer_postal_code',
        ]);
    });
}

};
