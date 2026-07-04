<?php

namespace App\Support;

use App\Models\Quote;

/**
 * Admin-curated pull-quotes for fixed page slots (mission, vision-1,
 * vision-2). A page asks for its slot; null means the caller renders its
 * lang-string fallback, so an empty quotes table changes nothing visually.
 */
class Quotes
{
    public function forSlot(string $slot): ?Quote
    {
        return Quote::query()
            ->where('slot', $slot)
            ->where('visible', true)
            ->first();
    }
}
