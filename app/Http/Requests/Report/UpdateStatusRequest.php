<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $report = $this->route('report');

        return $report !== null && $this->user()?->can('manageResponse', $report) === true;
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(['terverifikasi', 'diteruskan', 'dalam_penanganan', 'terkendali', 'selesai', 'ditolak', 'padam_sendiri', 'duplikat', 'baru'])], 'note' => ['nullable', 'string', 'max:1000']];
    }
}
