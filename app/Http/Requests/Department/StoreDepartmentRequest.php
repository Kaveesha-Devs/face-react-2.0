<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // FIX: Allow if user is Admin/SuperAdmin OR belongs to a company
        return $this->user()->hasAdminAccess() || $this->user()->company_id !== null;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:100'],
            'name_sinhala' => ['nullable', 'string', 'max:100'],
            'name_tamil'   => ['nullable', 'string', 'max:100'],
            'code'         => ['nullable', 'string', 'max:20'],
        ];
    }
}