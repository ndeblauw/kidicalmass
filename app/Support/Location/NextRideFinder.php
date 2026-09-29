<?php

namespace App\Support\Location;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Concerns\LocalizesFields;
use App\Models\PostalCode;
use App\Support\RideDate;

class NextRideFinder
{
    /**
     * What <x-ride-day> / <x-ride-row> and the proximity partition read. Every
     * locale's title and venue, since the record's language is decided per locale
     * ({@see LocalizesFields}).
     *
     * @var list<string>
     */
    private const COLUMNS = [
        'id', 'activity_type', 'begin_date', 'duration_minutes', 'postal_code',
        'title_nl', 'title_fr', 'title_en', 'location_nl', 'location_fr', 'location_en',
    ];

    /**
     * Resolve the rides for the homepage's "De volgende ritten bij jou": up to the 3
     * soonest within the nearby radius, otherwise the 3 nearest anywhere (flagged far).
     * `ride` is the single nearest, kept for the distance/far messaging. Returns
     * ride=null when no location is set (picker state) or when there are no upcoming
     * rides at all (off-season); `upcoming_preview` holds the (up to 3) rides to list,
     * as day cards in display order.
     *
     * @param  array{zip: string, lat: float, lng: float, name: string}|null  $location
     * @return array{ride: Activity|null, distance_km: float|null, is_far: bool, has_upcoming: bool, upcoming_preview: array<int, array{date: string, rows: array<int, array{item: Activity, distance_km?: float|null}>}>}
     */
    public static function find(?array $location): array
    {
        // Only the columns a ride row reads (title, venue, type, date, postcode for
        // the distance). Without a location only the 3 soonest are listed; with one,
        // every upcoming ride is a candidate for the nearest.
        $upcoming = Activity::query()
            ->select(self::COLUMNS)
            ->published()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->where('begin_date', '>=', RideDate::startOfToday())
            ->orderBy('begin_date')
            ->when($location === null, fn ($query) => $query->limit(3))
            ->get();

        if ($upcoming->isEmpty()) {
            return ['ride' => null, 'distance_km' => null, 'is_far' => false, 'has_upcoming' => false, 'upcoming_preview' => []];
        }

        $preview = self::groupIntoDays(
            $upcoming->take(3)->map(fn (Activity $activity): array => ['item' => $activity])->all()
        );

        if (! $location) {
            return ['ride' => null, 'distance_km' => null, 'is_far' => false, 'has_upcoming' => true, 'upcoming_preview' => $preview];
        }

        $coordsByZip = PostalCode::whereIn('zip', $upcoming->pluck('postal_code')->filter()->unique())
            ->get(['zip', 'latitude', 'longitude'])->keyBy('zip');

        $partition = Proximity::partitionByRadius(
            $upcoming,
            ['lat' => $location['lat'], 'lng' => $location['lng']],
            (float) config('location.nearby_radius_km'),
            function (Activity $activity) use ($coordsByZip) {
                $pc = $activity->postal_code ? $coordsByZip->get($activity->postal_code) : null;

                return $pc ? ['lat' => $pc->latitude, 'lng' => $pc->longitude] : null;
            },
        );

        $nearby = $partition['nearby'];

        // Show the 3 soonest rides near you (date-ordered: partition preserves input
        // order). When nothing is in range, fall back to the 3 nearest rides anywhere,
        // flagged far rather than hidden; rides without coordinates go last, and ties
        // keep date order. Every row keeps its distance so the list can show it.
        $source = $nearby->isNotEmpty()
            ? $nearby
            : $partition['far']->sortBy(fn (array $row): float => $row['distance_km'] ?? INF)->values();
        $chosen = $source->first();

        $nearbyPreview = self::groupIntoDays($source->take(3)->all());

        return [
            'ride' => $chosen['item'],
            'distance_km' => $chosen['distance_km'],
            // A ride whose postal_code can't be resolved to coordinates falls into the
            // "far" bucket (distance_km null), so it is reported as far rather than hidden.
            'is_far' => $nearby->isEmpty(),
            'has_upcoming' => true,
            'upcoming_preview' => $nearbyPreview,
        ];
    }

    /**
     * Group rows into day cards without reordering them: only neighbouring rows that
     * share a date join one card. Date-ordered input gives one card per day; the
     * distance-ordered far list stays nearest-first even when a date repeats.
     *
     * @param  array<int, array{item: Activity, distance_km?: float|null}>  $rows
     * @return array<int, array{date: string, rows: array<int, array{item: Activity, distance_km?: float|null}>}>
     */
    private static function groupIntoDays(array $rows): array
    {
        $days = [];

        foreach ($rows as $row) {
            $date = $row['item']->begin_date->toDateString();
            $last = array_key_last($days);

            if ($last !== null && $days[$last]['date'] === $date) {
                $days[$last]['rows'][] = $row;
            } else {
                $days[] = ['date' => $date, 'rows' => [$row]];
            }
        }

        return $days;
    }
}
