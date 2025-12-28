<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Page 1 - User details (if new customer)
            $table->string('customer_first_name')->nullable()->after('user_id');
            $table->string('customer_last_name')->nullable()->after('customer_first_name');
            $table->string('customer_password')->nullable()->after('customer_last_name');
            $table->string('customer_aadhar_number', 12)->nullable()->after('customer_password');
            $table->string('customer_mobile_number', 15)->nullable()->after('customer_aadhar_number');
            $table->string('customer_alternative_mobile', 15)->nullable()->after('customer_mobile_number');
            $table->string('customer_email')->nullable()->after('customer_alternative_mobile');
            $table->string('customer_pan_number', 10)->nullable()->after('customer_email');
            $table->enum('customer_address_type', ['RESIDENTIAL', 'PERMANENT', 'OFFICE'])->nullable()->after('customer_pan_number');
            $table->enum('customer_employment_type', ['self_employed', 'salaried'])->nullable()->after('customer_address_type');
            
            // Page 2 - Additional details
            $table->string('mobile_otp')->nullable()->after('customer_employment_type');
            $table->text('office_address')->nullable()->after('mobile_otp');
            $table->string('cibil_score')->nullable()->after('office_address');
            $table->text('cibil_details')->nullable()->after('cibil_score');
            
            // Page 3 - Sanction and Bank
            $table->string('aadhar_otp')->nullable()->after('cibil_details');
            $table->text('sanction_letter')->nullable()->after('aadhar_otp');
            $table->enum('vehicle_type', ['new', 'used'])->default('new')->after('sanction_letter');
            
            // Penalty configuration
            $table->integer('max_penalty_applications')->default(3)->after('custom_penalty_amount');
            $table->json('penalty_dates')->nullable()->after('max_penalty_applications'); // Store dates like [10, 12, 15]
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'customer_first_name',
                'customer_last_name',
                'customer_password',
                'customer_aadhar_number',
                'customer_mobile_number',
                'customer_alternative_mobile',
                'customer_email',
                'customer_pan_number',
                'customer_address_type',
                'customer_employment_type',
                'mobile_otp',
                'office_address',
                'cibil_score',
                'cibil_details',
                'aadhar_otp',
                'sanction_letter',
                'vehicle_type',
                'max_penalty_applications',
                'penalty_dates',
            ]);
        });
    }
};
