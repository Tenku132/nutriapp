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
        if (!Schema::hasColumn('users', 'business_name')) {
            $table->string('business_name')->nullable();
        }
        if (!Schema::hasColumn('users', 'location')) {
            $table->string('location')->nullable();
        }
        if (!Schema::hasColumn('users', 'bio')) {
            $table->text('bio')->nullable();
        }
        // Remove 'phone' here since it already exists
    });
}


public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['business_name', 'location', 'bio']);
    });
}
};
