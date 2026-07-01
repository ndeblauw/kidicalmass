<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PressArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title_nl' => ['required', 'string', 'max:255'],
            'title_fr' => ['required', 'string', 'max:255'],
            'outlet' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'author_id' => ['nullable', 'integer', 'exists:users,id'],
            'activities' => ['nullable', 'array'],
            'activities.*' => ['integer', 'exists:activities,id'],
            'articles' => ['nullable', 'array'],
            'articles.*' => ['integer', 'exists:articles,id'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['integer', 'exists:groups,id'],
            'document' => ['nullable', 'array'],
            'document.*' => ['string'],
        ];
    }
}
