<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title_nl' => ['required', 'string', 'max:255'],
            'title_fr' => ['required', 'string', 'max:255'],
            'content_nl' => ['required', 'string'],
            'content_fr' => ['required', 'string'],
            'begin_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:begin_date'],
            'location' => ['required', 'string', 'max:255'],
            'author_id' => ['required', 'exists:users,id'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['exists:groups,id'],
        ];
    }
}
