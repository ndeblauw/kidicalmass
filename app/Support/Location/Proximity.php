<?php

namespace App\Support\Location;

use Illuminate\Support\Collection;
use InvalidArgumentException;

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
     * in both groups; callers are responsible for sorting before passing items in.
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
        return static::partitionByBands($items, $origin, ['nearby' => $radiusKm], $coordsOf);
    }

    /**
     * Split a collection into ordered distance bands, each row annotated with
     * `['item' => $original, 'distance_km' => float|null]`. A row lands in the first
     * band whose (inclusive) upper bound covers its distance. Rows beyond the last
     * bound, and rows whose coordinates resolve to null, land in a trailing `far`
     * band, so nothing is ever dropped. Input order is preserved in every band.
     *
     * @template T
     *
     * @param  Collection<int, T>  $items
     * @param  array{lat: float, lng: float}  $origin
     * @param  array<string, float|int>  $bands  name => inclusive upper bound in km, ascending
     * @param  callable(T): (array{lat: float, lng: float}|null)  $coordsOf
     * @return array<string, Collection<int, array{item: T, distance_km: float|null}>>
     *
     * @throws InvalidArgumentException when a caller band is named `far` (reserved)
     */
    public static function partitionByBands(Collection $items, array $origin, array $bands, callable $coordsOf): array
    {
        if (array_key_exists('far', $bands)) {
            throw new InvalidArgumentException('The band name "far" is reserved for the overflow bucket.');
        }

        $partitions = array_map(fn () => new Collection, $bands) + ['far' => new Collection];

        foreach ($items as $item) {
            $coords = $coordsOf($item);
            $distanceKm = $coords ? round(static::distanceKm($origin, $coords), 1) : null;

            $band = 'far';
            if ($distanceKm !== null) {
                foreach ($bands as $name => $boundKm) {
                    if ($distanceKm <= $boundKm) {
                        $band = $name;
                        break;
                    }
                }
            }

            $partitions[$band]->push(['item' => $item, 'distance_km' => $distanceKm]);
        }

        return $partitions;
    }

    /**
     * The $count items closest to $origin, annotated with distance and sorted ascending.
     * Items whose coordinates resolve to null are dropped (they cannot be ranked).
     *
     * @template T
     *
     * @param  Collection<int, T>  $items
     * @param  array{lat: float, lng: float}  $origin
     * @param  callable(T): (array{lat: float, lng: float}|null)  $coordsOf
     * @return Collection<int, array{item: T, distance_km: float}>
     */
    public static function nearest(Collection $items, array $origin, int $count, callable $coordsOf): Collection
    {
        return $items
            ->map(function ($item) use ($origin, $coordsOf) {
                $coords = $coordsOf($item);

                return $coords === null
                    ? null
                    : ['item' => $item, 'distance_km' => round(static::distanceKm($origin, $coords), 1)];
            })
            ->filter()
            ->sortBy('distance_km')
            ->take($count)
            ->values();
    }
}
