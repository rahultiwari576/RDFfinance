<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guarantors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('aadhar_number', 12);
            $table->string('pan_number', 10);
            $table->string('mobile_number', 15);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guarantors');
    }
};
