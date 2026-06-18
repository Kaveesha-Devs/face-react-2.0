<?php

namespace App\Http\Requests\React;

use Illuminate\Foundation\Http\FormRequest;

class SubmitReactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'react_type_id' => ['required', 'integer', 'exists:react_types,id'],
            'note'          => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'react_type_id.required' => 'Please select a reaction.',
            'react_type_id.exists'   => 'Invalid reaction type.',
        ];
    }
}