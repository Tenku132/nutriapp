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
    Schema::table('ratings', function (Blueprint $table) {
        if (Schema::hasColumn('ratings', 'buyer_id')) {
            $table->renameColumn('buyer_id', 'user_id');
        }
    });
}

public function down()
{
    Schema::table('ratings', function (Blueprint $table) {
        $table->renameColumn('user_id', 'buyer_id');
    });
}

};
