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
}
