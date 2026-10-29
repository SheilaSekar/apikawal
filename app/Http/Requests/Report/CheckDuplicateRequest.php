<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class CheckDuplicateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && (bool) $user->newQuery()->whereKey($user->getKey())->value('is_active');
    }

    public function rules(): array
    {
        return ['lat' => ['required', 'numeric', 'between:-90,90'], 'lng' => ['required', 'numeric', 'between:-180,180']];
    }
}
