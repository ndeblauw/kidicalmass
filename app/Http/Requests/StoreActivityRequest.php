<?php

namespace App\Http\Requests;

use App\Enums\ActivityType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title_nl' => ['required', 'string', 'max:255'],
            'title_fr' => ['required', 'string', 'max:255'],
            'content_nl' => ['required', 'string'],
            'content_fr' => ['required', 'string'],
            'activity_type' => ['required', Rule::enum(ActivityType::class)],
            'begin_date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'author_id' => ['required', 'exists:users,id'],
            'organizer_id' => ['nullable', 'exists:users,id'],
            'commute_link' => ['nullable', 'url', 'max:500'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['exists:groups,id'],
        ];
    }
}
