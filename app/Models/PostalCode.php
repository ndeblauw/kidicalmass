<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Unguarded]
class PostalCode extends Model
{
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    public static function coordinatesFor(string $zip): ?array
    {
        $row = static::where('zip', $zip)->first();

        if (! $row) {
            return null;
        }

        return ['lat' => $row->latitude, 'lng' => $row->longitude];
    }

    public static function nearestTo(float $lat, float $lng): ?self
    {
        return static::all()
            ->sortBy(fn (self $pc): float => ($pc->latitude - $lat) ** 2 + ($pc->longitude - $lng) ** 2)
            ->first();
    }
}
