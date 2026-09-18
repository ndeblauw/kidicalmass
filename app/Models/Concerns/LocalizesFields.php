<?php

namespace App\Models\Concerns;

/**
 * Serves a model's translatable fields in the active locale, with French as the
 * only fallback (French is the source language, per the FR-on-staging wayfinder).
 *
 * The record's language is decided by one field (localizingField()), so a record
 * never mixes languages: when the deciding field has no value in the active
 * locale, every field falls back to French — or is empty when French is empty
 * too. Dutch content is never shown on /fr.
 */
trait LocalizesFields
{
    /**
     * The field that decides a record's language. Override per model when the
     * primary content field is not `title`.
     */
    protected function localizingField(): string
    {
        return 'title';
    }

    public function recordLocale(): string
    {
        $locale = app()->getLocale();
        $deciding = $this->localizingField();

        return filled($this->{$deciding.'_'.$locale}) ? $locale : 'fr';
    }

    public function localizedValue(string $field): ?string
    {
        $value = $this->{$field.'_'.$this->recordLocale()};

        return $value === null ? null : (string) $value;
    }
}
