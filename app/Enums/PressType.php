<?php

namespace App\Enums;

enum PressType: string
{
    case NEWSPAPER = 'newspaper';
    case MAGAZINE = 'magazine';
    case ONLINE = 'online';
    case TV = 'tv';
    case RADIO = 'radio';

    public function label(): string
    {
        return match ($this) {
            self::NEWSPAPER => 'Newspaper',
            self::MAGAZINE => 'Magazine',
            self::ONLINE => 'Online',
            self::TV => 'TV',
            self::RADIO => 'Radio',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::NEWSPAPER => 'bg-gray-100 text-gray-800',
            self::MAGAZINE => 'bg-purple-100 text-purple-800',
            self::ONLINE => 'bg-blue-100 text-blue-800',
            self::TV => 'bg-red-100 text-red-800',
            self::RADIO => 'bg-yellow-100 text-yellow-800',
        };
    }

    /**
     * Get an array of options for use in forms and filters
     */
    public static function getOptionsArray(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->toArray();
    }
}
