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
        $table->string('payout_method_secondary')->nullable()->after('payout_account');
        $table->string('payout_account_secondary')->nullable()->after('payout_method_secondary');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['payout_method_secondary', 'payout_account_secondary']);
    });
}

};
