<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && (bool) $user->newQuery()->whereKey($user->getKey())->value('is_active');
    }

    public function rules(): array
    {
        return [
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'land_type' => ['required', Rule::in(['gambut', 'hutan', 'semak', 'kebun', 'lainnya'])],
            'signs' => ['required', 'array', 'min:1'],
            'signs.*' => ['required', Rule::in(['asap', 'api', 'bara'])],
            'estimated_area_ha' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'photos' => ['nullable', 'array', 'max:3'],
            'photos.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
