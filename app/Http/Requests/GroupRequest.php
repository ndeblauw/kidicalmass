<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Ndeblauw\BlueAdmin\Models\Filepond;

class GroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shortname' => ['required', 'string', 'max:255', Rule::unique('groups', 'shortname')->ignore($this->group)],
            'name_nl' => ['required_without:name_fr', 'string', 'max:255'],
            'name_fr' => ['required_without:name_nl', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'intro_nl' => ['nullable', 'string', 'max:500'],
            'intro_fr' => ['nullable', 'string', 'max:500'],
            'zip' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:groups,id'],
            'invisible' => ['boolean'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'main' => ['nullable', 'array'],
            'main.*' => ['string'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['string'],
            'downloads' => ['nullable', 'array'],
            'downloads.*' => ['string', $this->downloadIsAllowedType(...)],
        ];
    }

    /**
     * Downloads accept PDFs and images only (mirrors the 'downloads' media
     * collection), so a wrong file type becomes a form error, not a 500.
     * Already-stored files come back as `existing_file_{id}` and are skipped.
     */
    private function downloadIsAllowedType(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! is_string($value) || str_starts_with($value, 'existing_file_')) {
            return;
        }

        try {
            $mime = File::mimeType(app(Filepond::class)->getPathFromServerId($value));
        } catch (\Throwable) {
            $fail('The download could not be read.');

            return;
        }

        if (! in_array($mime, ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'], true)) {
            $fail('Downloads must be a PDF, JPG, PNG or WebP file.');
        }
    }
}
