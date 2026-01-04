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
            if (!Schema::hasColumn('users', 'flat_building')) {
                $table->string('flat_building')->nullable();
            }
            if (!Schema::hasColumn('users', 'locality')) {
                $table->string('locality')->nullable();
            }
            if (!Schema::hasColumn('users', 'pincode')) {
                $table->string('pincode')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['flat_building', 'locality', 'pincode']);
        });
    }
};
