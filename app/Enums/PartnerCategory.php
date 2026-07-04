<?php

namespace App\Enums;

enum PartnerCategory: string
{
    case INSTITUTIONEEL = 'institutioneel';
    case BONDGENOOT = 'bondgenoot';
    case OPERATIONEEL = 'operationeel';

    public function label(): string
    {
        return match ($this) {
            self::INSTITUTIONEEL => 'Institutioneel',
            self::BONDGENOOT => 'Bondgenoot',
            self::OPERATIONEEL => 'Operationeel',
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
