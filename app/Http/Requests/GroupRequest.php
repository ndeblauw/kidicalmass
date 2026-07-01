<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shortname' => ['required', 'string', 'max:255', Rule::unique('groups', 'shortname')->ignore($this->group)],
            'name' => ['required', 'string', 'max:255'],
            'zip' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:groups,id'],
            'invisible' => ['boolean'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'main' => ['nullable', 'array'],
            'main.*' => ['string'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['string'],
        ];
    }
}
