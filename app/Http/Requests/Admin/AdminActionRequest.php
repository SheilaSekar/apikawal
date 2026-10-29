<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AdminActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage', User::class) === true;
    }

    public function rules(): array
    {
        return match ($this->route()->getName()) {
            'admin.users.update' => ['role' => ['required', 'in:warga,relawan,petugas,admin'], 'is_active' => ['required', 'boolean'], 'volunteer_status' => ['nullable', 'in:none,pending,approved,rejected']],
            'admin.moderation.approve' => ['approved' => ['required', 'boolean'], 'reason' => ['required_if:approved,0', 'nullable', 'string', 'min:5', 'max:500']],
            'admin.hotspots.import' => ['file' => ['required', 'file', 'mimes:csv,txt', 'max:10240']],
            default => [],
        };
    }
}
