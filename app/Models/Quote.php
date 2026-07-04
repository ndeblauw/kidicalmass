<?php

namespace App\Models;

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

    protected function casts(): array
    {
        return [
            'visible' => 'boolean',
        ];
    }
}
