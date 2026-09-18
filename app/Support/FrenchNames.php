<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Helpers for French municipality names around inserted names, per the
 * "Group names, slugs and prepositions in French" decision: elide "de" to
 * "d'" before a name starting with a vowel, and slugify a municipality's name
 * (ASCII, hyphens) for use in URLs.
 */
class FrenchNames
{
    /**
     * Turn "de {name}" into "d'{name}" before a name that starts with a vowel
     * or a silent h, leaving other names as "de {name}".
     */
    public static function preposition(string $name): string
    {
        return preg_match('/^[aeiouyàâäéèêëîïôöùûüæœh]/i', $name) === 1
            ? "d'".$name
            : 'de '.$name;
    }

    /**
     * A municipality's name as an ASCII slug: lowercased, accents stripped,
     * spaces and punctuation collapsed to single hyphens.
     */
    public static function slug(string $name): string
    {
        return Str::slug($name);
    }
}
