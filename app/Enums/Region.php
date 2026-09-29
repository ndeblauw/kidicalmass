<?php

namespace App\Enums;

use App\Models\Group;

/**
 * The three Belgian regions a local group hangs under, keyed by the invisible
 * parent group's `name_nl` (the locale-independent key the groups finder has
 * always filtered on). The single source of truth for region -> colour on
 * every map and list dot: the interactive Leaflet map on Lokale groepen, its
 * list/filter dots, and the static <x-belgium-map>.
 *
 * Colours are CSS custom-property names (theme tokens), never raw values, so
 * the palette stays in app.css.
 */
enum Region: string
{
    case BRUSSELS = 'Brussels Capital Region';
    case WALLONIA = 'Wallonia';
    case FLANDERS = 'Flanders';

    /** Colour for a group without a (known) region parent. */
    public const FALLBACK_COLOR_TOKEN = '--color-kidical-red';

    public static function forGroup(Group $group): ?self
    {
        return self::tryFrom((string) $group->parent?->name_nl);
    }

    /** Theme token for this region, e.g. "--color-kidical-blue". */
    public function colorToken(): string
    {
        return match ($this) {
            self::BRUSSELS => '--color-kidical-blue',
            self::WALLONIA => '--color-kidical-orange',
            self::FLANDERS => '--color-kidical-green',
        };
    }

    /** Colour token for an optional region, falling back like the interactive map does. */
    public static function colorTokenFor(?self $region): string
    {
        return $region?->colorToken() ?? self::FALLBACK_COLOR_TOKEN;
    }

    /** Short, stable slug for data-* hooks: "brussels" | "wallonia" | "flanders". */
    public function slug(): string
    {
        return strtolower($this->name);
    }

    /** Localised short name ("Brussel" / "Bruxelles"). */
    public function label(): string
    {
        return __('groups.regions.'.$this->value);
    }
}
