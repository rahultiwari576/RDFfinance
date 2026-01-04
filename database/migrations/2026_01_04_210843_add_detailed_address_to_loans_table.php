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
        Schema::table('loans', function (Blueprint $table) {
            $table->string('customer_flat_building')->nullable()->after('customer_address_type');
            $table->string('customer_locality')->nullable()->after('customer_flat_building');
            $table->string('customer_city')->nullable()->after('customer_locality');
            $table->string('customer_state')->nullable()->after('customer_city');
            $table->string('customer_pincode')->nullable()->after('customer_state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'customer_flat_building',
                'customer_locality',
                'customer_city',
                'customer_state',
                'customer_pincode',
            ]);
        });
    }
};
