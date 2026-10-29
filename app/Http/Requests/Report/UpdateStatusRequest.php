<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role?->value, ['petugas', 'admin'], true);
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(['terverifikasi', 'diteruskan', 'dalam_penanganan', 'terkendali', 'selesai', 'ditolak', 'padam_sendiri', 'duplikat', 'baru'])], 'note' => ['nullable', 'string', 'max:1000']];
    }
}
