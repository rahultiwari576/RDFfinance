<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('aadhar_number', 12)->unique();
            $table->string('pan_number', 10);
            $table->string('phone_number', 15);
            $table->unsignedTinyInteger('age');
            $table->string('aadhar_document_path')->nullable();
            $table->string('pan_document_path')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

