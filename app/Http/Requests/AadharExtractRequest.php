<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AadharExtractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'aadhar_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}

