<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->enum('vehicle_type', ['new', 'used'])->default('new');
            $table->string('company_name')->nullable();
            $table->string('model_name')->nullable();
            
            // RC Details (for used vehicles)
            $table->string('engine_number')->nullable();
            $table->string('chassis_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->date('registration_date')->nullable();
            $table->date('registration_validity')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('vehicle_color')->nullable();
            $table->text('rc_other_details')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_details');
    }
};
