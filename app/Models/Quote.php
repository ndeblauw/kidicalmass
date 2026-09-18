<?php

namespace App\Models;

use App\Models\Concerns\LocalizesFields;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * An admin-curated parent quote for a fixed page slot (mission, vision-1,
 * vision-2). Pages fall back to their lang string when a slot is empty,
 * so this table can stay empty without any visual change.
 */
#[Unguarded]
class Quote extends Model
{
    use HasFactory;
    use LocalizesFields;

    protected function localizingField(): string
    {
        return 'quote';
    }

    public function getQuoteAttribute(): ?string
    {
        return $this->localizedValue('quote');
    }

    public function getAttributionAttribute(): ?string
    {
        return $this->localizedValue('attribution');
    }

    protected function casts(): array
    {
        return [
            'visible' => 'boolean',
        ];
    }
}
