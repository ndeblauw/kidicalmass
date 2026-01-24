<?php

namespace App\Enums;

enum ActivityType: string
{
    case KIDICALMASS = 'kidicalmass';
    case MEETING = 'meeting';
    case WORKSHOP = 'workshop';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::KIDICALMASS => 'Kidicalmass',
            self::MEETING => 'Meeting',
            self::WORKSHOP => 'Workshop',
            self::OTHER => 'Other',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::KIDICALMASS => 'green',
            self::MEETING => 'blue',
            self::WORKSHOP => 'yellow',
            self::OTHER => 'gray',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::KIDICALMASS => 'bg-green-100 text-green-800',
            self::MEETING => 'bg-blue-100 text-blue-800',
            self::WORKSHOP => 'bg-yellow-100 text-yellow-800',
            self::OTHER => 'bg-gray-100 text-gray-800',
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
