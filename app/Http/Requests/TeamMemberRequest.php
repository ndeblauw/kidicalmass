<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'bio_nl' => ['nullable', 'string'],
            'bio_fr' => ['nullable', 'string'],
            'sort' => ['nullable', 'integer'],
            'visible' => ['boolean'],
            'photo' => ['nullable', 'array'],
            'photo.*' => ['string'],
        ];
    }
}
