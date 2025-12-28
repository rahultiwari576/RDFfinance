<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'principal_amount',
        'interest_rate',
        'tenure_months',
        'emi_amount',
        'total_repayment',
        'status',
        'next_due_date',
        'penalty_amount',
        'custom_penalty_amount',
        // New loan application fields
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
    ];

    protected $casts = [
        'next_due_date' => 'date',
        'penalty_dates' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(LoanInstallment::class);
    }

    public function guarantor()
    {
        return $this->hasOne(Guarantor::class);
    }

    public function vehicleDetail()
    {
        return $this->hasOne(VehicleDetail::class);
    }

    public function references()
    {
        return $this->hasMany(Reference::class);
    }

    public function bankDetail()
    {
        return $this->hasOne(BankDetail::class);
    }

    public function documents()
    {
        return $this->hasMany(LoanDocument::class);
    }
}

