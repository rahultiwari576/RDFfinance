<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'mother_name',
        'father_name',
        'gender',
        'dob',
        'email',
        'password',
        'role',
        'aadhar_number',
        'alternative_phone_number',
        'address_type',
        'flat_building',
        'locality',
        'state',
        'city',
        'pincode',
        'employment_type',
        'phone_number',
        'pan_number',
        'age',
        'profession',
        'education',
        'additional_info',
        'aadhar_document_path',
        'pan_document_path',
        'driving_license_path',
        'address',
        'area',
        'zip_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}

