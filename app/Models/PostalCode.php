<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
     * Centroid rows for a set of zips in one query, keyed by zip. A zip with
     * several localities (4000 Liège) resolves to one row, so every map that
     * places groups (Lokale groepen, <x-belgium-map>) lands them on the same spot.
     *
     * @param  iterable<int, string|null>  $zips
     * @return Collection<string, self>
     */
    public static function centroidsByZip(iterable $zips): Collection
    {
        $zips = collect($zips)->filter()->unique()->values();

        if ($zips->isEmpty()) {
            return collect();
        }

        return static::whereIn('zip', $zips)->orderBy('id')->get()->keyBy('zip');
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
