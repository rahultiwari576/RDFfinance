<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'principal_amount' => ['required', 'numeric', 'min:1000'],
            'interest_rate' => ['required', 'numeric', 'min:1'],
            'tenure_months' => ['required', 'integer', 'min:1'],
            'custom_penalty_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

