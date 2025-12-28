<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guarantor extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'first_name',
        'last_name',
        'aadhar_number',
        'pan_number',
        'mobile_number',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
