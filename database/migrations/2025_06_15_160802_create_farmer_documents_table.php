<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('farmer_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('users')->onDelete('cascade');
            $table->string('document_path');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('admin_note')->nullable(); // Reason if rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmer_documents');
    }
};
