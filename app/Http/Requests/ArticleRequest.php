<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
            'author_id' => ['required', 'integer', 'exists:users,id'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['integer', 'exists:groups,id'],
            'main' => ['nullable', 'string'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['string'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
