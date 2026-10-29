<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePriorityWeightsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->value === 'admin';
    }

    public function rules(): array
    {
        return [
            'weights' => ['required', 'array', 'size:6'],
            'weights.w_verification' => ['required', 'numeric', 'min:0', 'max:100'],
            'weights.w_landtype' => ['required', 'numeric', 'min:0', 'max:100'],
            'weights.w_hotspot' => ['required', 'numeric', 'min:0', 'max:100'],
            'weights.w_poi' => ['required', 'numeric', 'min:0', 'max:100'],
            'weights.w_support' => ['required', 'numeric', 'min:0', 'max:100'],
            'weights.w_photo' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function after(): array
    {
        return [function ($validator) {
            if ((float) collect($this->input('weights', []))->sum() !== 100.0) {
                $validator->errors()->add('weights', 'Jumlah bobot harus tepat 100.');
            }
        }];
    }
}
