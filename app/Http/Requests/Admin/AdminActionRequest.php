<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->value === 'admin';
    }

    public function rules(): array
    {
        return match ($this->route()->getName()) {
            'admin.users.update' => ['role' => ['required', 'in:warga,relawan,petugas,admin'], 'is_active' => ['required', 'boolean']],
            'admin.moderation.approve' => ['approved' => ['required', 'boolean']],
            'admin.hotspots.import' => ['file' => ['required', 'file', 'mimes:csv,txt', 'max:10240']],
            default => [],
        };
    }
}
