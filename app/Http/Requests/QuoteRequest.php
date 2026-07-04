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
            'quote' => ['required', 'string'],
            'attribution' => ['required', 'string', 'max:255'],
            'visible' => ['boolean'],
        ];
    }
}
