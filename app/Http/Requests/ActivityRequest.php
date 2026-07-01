<?php

namespace App\Http\Requests;

use App\Enums\ActivityType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title_nl' => ['required_without:title_fr', 'string', 'max:255'],
            'title_fr' => ['required_without:title_nl', 'string', 'max:255'],
            'content_nl' => ['required_without:content_fr', 'string'],
            'content_fr' => ['required_without:content_nl', 'string'],
            'activity_type' => ['required', Rule::enum(ActivityType::class)],
            'begin_date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'distance' => ['nullable', 'string', 'max:50'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'commute_link' => ['nullable', 'url', 'max:500'],
            'komoot_url' => ['nullable', 'url', 'max:500'],
            'author_id' => ['required', 'integer', 'exists:users,id'],
            'organizer_id' => ['nullable', 'integer', 'exists:users,id'],
            'is_published' => ['boolean'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['integer', 'exists:groups,id'],
            'main' => ['nullable', 'array'],
            'main.*' => ['string'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['string'],
            'gpx' => ['nullable', 'array'],
            'gpx.*' => ['string'],
        ];
    }
}
