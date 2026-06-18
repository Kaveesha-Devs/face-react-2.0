<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:200'],
            'email'          => ['required', 'email', 'unique:companies,email'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'address'        => ['nullable', 'string', 'max:2048'],
            'admin_name'     => ['required', 'string', 'max:200'],
            'admin_username' => ['required', 'string', 'max:50', 'unique:users,username', 'alpha_dash'],
            'admin_password' => ['required', 'string', 'min:8'],
        ];
    }
}