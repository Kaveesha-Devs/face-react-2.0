<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAdminAccess();
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:100'],
            'username'      => ['required', 'string', 'max:50', 'unique:users,username', 'alpha_dash'],
            'email'         => ['nullable', 'email', 'unique:users,email'],
            'password'      => ['required', 'string', 'min:6'],
            'employee_id'   => ['nullable', 'string', 'max:50'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'section_id'    => ['nullable', 'integer', 'exists:sections,id'],
            'language'      => ['nullable', 'in:en,si,ta'],
            'avatar'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ];
    }
}