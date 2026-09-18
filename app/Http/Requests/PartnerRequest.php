<?php

namespace App\Http\Requests;

use App\Enums\PartnerCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_nl' => ['required_without:name_fr', 'string', 'max:255'],
            'name_fr' => ['required_without:name_nl', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'description_nl' => ['nullable', 'string'],
            'description_fr' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'group_id' => ['required', 'integer', 'exists:groups,id'],
            'category' => ['nullable', Rule::enum(PartnerCategory::class)],
            'show_logo' => ['boolean'],
            'visible' => ['boolean'],
            'logo' => ['nullable', 'array'],
            'logo.*' => ['string'],
        ];
    }
}
