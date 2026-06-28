<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->isMethod('post') && ! filled($this->input('password'))) {
            $this->request->remove('password');
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user)],
            'password' => [$this->isMethod('post') ? 'required' : 'sometimes', 'string', 'min:8'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['integer', 'exists:groups,id'],
        ];
    }
}
