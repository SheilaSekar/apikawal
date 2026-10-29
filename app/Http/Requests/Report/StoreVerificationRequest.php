<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['vote' => ['required', Rule::in(['konfirmasi', 'tidak_terlihat', 'sudah_padam'])], 'distance_km' => ['nullable', 'numeric', 'min:0', 'max:20000']];
    }
}
