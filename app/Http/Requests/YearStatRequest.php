<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class YearStatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'year' => ['required', 'integer', 'min:2020', 'max:2100', Rule::unique('year_stats', 'year')->ignore($this->year_stat)],
            'participants' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
