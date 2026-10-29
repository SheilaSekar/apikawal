<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Enums\VolunteerStatus;
use Illuminate\Foundation\Http\FormRequest;

class VolunteerApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === Role::Warga
            && in_array($this->user()?->volunteer_status, [VolunteerStatus::None, VolunteerStatus::Rejected], true);
    }

    public function rules(): array
    {
        return ['motivation' => ['required', 'string', 'min:20', 'max:500']];
    }
}
