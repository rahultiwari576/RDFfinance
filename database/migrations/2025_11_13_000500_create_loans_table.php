<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('principal_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->unsignedInteger('tenure_months');
            $table->decimal('emi_amount', 12, 2);
            $table->decimal('total_repayment', 12, 2);
            $table->enum('status', ['active', 'completed', 'defaulted'])->default('active');
            $table->date('next_due_date')->nullable();
            $table->decimal('penalty_amount', 12, 2)->default(100);
            $table->decimal('custom_penalty_amount', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};

