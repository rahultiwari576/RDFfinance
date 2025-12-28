<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->integer('existing_user_id')->nullable();
            
            // Store form data as JSON
            $table->json('form_data')->nullable();
            
            // Store current step
            $table->integer('current_step')->default(1);
            
            // Timestamps
            $table->timestamps();
            
            // Index for quick lookup
            $table->index(['user_id', 'admin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_drafts');
    }
};
