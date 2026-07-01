<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'description_nl' => ['nullable', 'string'],
            'description_fr' => ['nullable', 'string'],
            'group_id' => ['required', 'integer', 'exists:groups,id'],
            'show_logo' => ['boolean'],
            'visible' => ['boolean'],
            'logo' => ['nullable', 'array'],
            'logo.*' => ['string'],
        ];
    }
}
