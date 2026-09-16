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
            'role_nl' => ['required_without:role_fr', 'string', 'max:255'],
            'role_fr' => ['required_without:role_nl', 'string', 'max:255'],
            'role_en' => ['nullable', 'string', 'max:255'],
            'bio_nl' => ['nullable', 'string'],
            'bio_fr' => ['nullable', 'string'],
            'bio_en' => ['nullable', 'string'],
            'sort' => ['nullable', 'integer'],
            'visible' => ['boolean'],
            'photo' => ['nullable', 'array'],
            'photo.*' => ['string'],
        ];
    }
}
