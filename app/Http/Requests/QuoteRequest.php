<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slot' => ['required', 'string', 'max:255', Rule::unique('quotes', 'slot')->ignore($this->route('quote'))],
            'quote_nl' => ['required_without:quote_fr', 'string'],
            'quote_fr' => ['required_without:quote_nl', 'string'],
            'quote_en' => ['nullable', 'string'],
            'attribution_nl' => ['required_without:attribution_fr', 'string', 'max:255'],
            'attribution_fr' => ['required_without:attribution_nl', 'string', 'max:255'],
            'attribution_en' => ['nullable', 'string', 'max:255'],
            'visible' => ['boolean'],
        ];
    }
}
