<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAdminAccess();
    }

    public function rules(): array
    {
        return [
            'name'         => ['sometimes', 'string', 'max:100'],
            'name_sinhala' => ['nullable', 'string', 'max:100'],
            'name_tamil'   => ['nullable', 'string', 'max:100'],
            'code'         => ['nullable', 'string', 'max:20'],
            'is_active'    => ['nullable', 'boolean'],
        ];
    }
}