<?php

namespace App\Support\Location;

use Illuminate\Support\Collection;

class Proximity
{
    /**
     * @param  array{lat: float, lng: float}  $from
     * @param  array{lat: float, lng: float}  $to
     */
    public static function distanceKm(array $from, array $to): float
    {
        $earthRadius = 6371.0;

        $dLat = deg2rad($to['lat'] - $from['lat']);
        $dLng = deg2rad($to['lng'] - $from['lng']);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($from['lat'])) * cos(deg2rad($to['lat'])) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * asin(min(1.0, sqrt($a)));
    }

    /**
     * Split a collection into nearby (<= radius) and far, each annotated with
     * `['item' => $original, 'distance_km' => float|null]`. Input order is preserved
     * in both groups — callers are responsible for sorting before passing items in.
     * Items whose coordinates resolve to null are always "far" (never hidden).
     *
     * @template T
     *
     * @param  Collection<int, T>  $items
     * @param  array{lat: float, lng: float}  $origin
     * @param  callable(T): (array{lat: float, lng: float}|null)  $coordsOf
     * @return array{nearby: Collection<int, array{item: T, distance_km: float}>, far: Collection<int, array{item: T, distance_km: float|null}>}
     */
    public static function partitionByRadius(Collection $items, array $origin, float $radiusKm, callable $coordsOf): array
    {
        $annotated = $items->map(function ($item) use ($origin, $coordsOf) {
            $coords = $coordsOf($item);

            return [
                'item' => $item,
                'distance_km' => $coords ? round(static::distanceKm($origin, $coords), 1) : null,
            ];
        });

        $nearby = $annotated
            ->filter(fn ($row) => $row['distance_km'] !== null && $row['distance_km'] <= $radiusKm)
            ->values();

        $far = $annotated
            ->reject(fn ($row) => $row['distance_km'] !== null && $row['distance_km'] <= $radiusKm)
            ->values();

        return ['nearby' => $nearby, 'far' => $far];
    }
}
