<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbuseFlagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['reason' => ['required', 'string', 'min:5', 'max:500']];
    }
}
