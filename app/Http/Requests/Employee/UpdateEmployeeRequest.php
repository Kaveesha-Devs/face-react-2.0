<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAdminAccess();
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee')->id;

        return [
            'name'          => ['sometimes', 'string', 'max:100'],
            'username'      => ['sometimes', 'string', 'max:50', 'unique:users,username,' . $employeeId, 'alpha_dash'],
            'email'         => ['nullable', 'email', 'unique:users,email,' . $employeeId],
            'password'      => ['nullable', 'string', 'min:6'],
            'employee_id'   => ['nullable', 'string', 'max:50'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'section_id'    => ['nullable', 'integer', 'exists:sections,id'],
            'language'      => ['nullable', 'in:en,si,ta'],
            'is_active'     => ['nullable', 'boolean'],
            'avatar'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ];
    }
}