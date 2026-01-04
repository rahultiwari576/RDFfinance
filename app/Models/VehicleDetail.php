<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'vehicle_type',
        'company_name',
        'model_name',
        'dealer_name',
        'dealer_mobile',
        'engine_number',
        'chassis_number',
        'registration_number',
        'registration_date',
        'registration_validity',
        'owner_name',
        'vehicle_color',
        'rc_other_details',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'registration_validity' => 'date',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
