<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slot' => ['required', 'string', 'max:255'],
            'quote' => ['required', 'string'],
            'attribution' => ['required', 'string', 'max:255'],
            'visible' => ['boolean'],
        ];
    }
}
