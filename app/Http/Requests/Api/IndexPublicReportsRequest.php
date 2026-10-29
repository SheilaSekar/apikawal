<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class IndexPublicReportsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['status' => ['nullable', 'in:baru,terverifikasi,diteruskan,dalam_penanganan,terkendali,selesai,padam_sendiri,duplikat'], 'level' => ['nullable', 'in:rendah,sedang,tinggi,kritis'], 'land_type' => ['nullable', 'in:gambut,hutan,semak,kebun,lainnya'], 'since' => ['nullable', 'date'], 'per_page' => ['nullable', 'integer', 'min:1', 'max:100']];
    }
}
