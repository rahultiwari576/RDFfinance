<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->enum('document_type', [
                'tax_invoice',
                'insurance',
                'delivery_photo',
                'aadhar_card',
                'pan_card',
                'address_proof',
                'rto_booklet',
                'cheque_1',
                'cheque_2',
                'cheque_3',
                'rc_document',
                'guarantor_aadhar',
                'guarantor_pan',
                'guarantor_cheque'
            ]);
            $table->string('file_path');
            $table->string('original_filename')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_documents');
    }
};
