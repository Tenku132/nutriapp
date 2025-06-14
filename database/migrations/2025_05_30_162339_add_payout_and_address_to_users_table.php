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
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'payout_method')) {
            $table->string('payout_method')->nullable();
        }
        if (!Schema::hasColumn('users', 'payout_account')) {
            $table->string('payout_account')->nullable();
        }
        // Address was already existing
    });
}



public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['payout_method', 'payout_account', 'address']);
    });
}
};
