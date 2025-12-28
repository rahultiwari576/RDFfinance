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
            // Split name into first_name, middle_name, last_name
            $table->string('first_name')->nullable()->after('name');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_name');
            
            // New fields after Aadhar number
            $table->string('alternative_phone_number', 15)->nullable()->after('aadhar_number');
            $table->enum('address_type', ['RESIDENTIAL', 'PERMANENT', 'OFFICE'])->nullable()->after('alternative_phone_number');
            $table->enum('employment_type', ['self_employed', 'salaried'])->nullable()->after('address_type');
            $table->string('driving_license_path')->nullable()->after('pan_document_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'middle_name',
                'last_name',
                'alternative_phone_number',
                'address_type',
                'employment_type',
                'driving_license_path',
            ]);
        });
    }
};
