<?php

namespace App\Support\Location;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;

class NextRideFinder
{
    /**
     * Resolve the rides for the homepage's "De volgende ritten bij jou": up to the 3
     * soonest within the nearby radius, otherwise the soonest anywhere (flagged far).
     * `ride` is the single nearest, kept for the distance/far messaging. Returns
     * ride=null when no location is set (picker state) or when there are no upcoming
     * rides at all (off-season); `upcoming_preview` holds the (up to 3) rides to list.
     *
     * @param  array{zip: string, lat: float, lng: float, name: string}|null  $location
     * @return array{ride: Activity|null, distance_km: float|null, is_far: bool, has_upcoming: bool, upcoming_preview: array<string, array<int, array{item: Activity}>>}
     */
    public static function find(?array $location): array
    {
        $upcoming = Activity::query()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->where('begin_date', '>=', now()->startOfDay())
            ->orderBy('begin_date')
            ->get();

        if ($upcoming->isEmpty()) {
            return ['ride' => null, 'distance_km' => null, 'is_far' => false, 'has_upcoming' => false, 'upcoming_preview' => []];
        }

        $preview = $upcoming->take(3)
            ->groupBy(fn (Activity $activity): string => $activity->begin_date->toDateString())
            ->map(fn ($group): array => $group->map(fn (Activity $activity): array => ['item' => $activity])->all())
            ->all();

        if (! $location) {
            return ['ride' => null, 'distance_km' => null, 'is_far' => false, 'has_upcoming' => true, 'upcoming_preview' => $preview];
        }

        $coordsByZip = PostalCode::whereIn('zip', $upcoming->pluck('postal_code')->filter()->unique())
            ->get()->keyBy('zip');

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

        // Show the 3 soonest rides near you (nearby first, date-ordered — partition
        // preserves input order). When nothing is in range, fall back to the soonest
        // rides anywhere, flagged far rather than hidden.
        $source = $nearby->isNotEmpty() ? $nearby : $partition['far'];
        $chosen = $source->first();

        $nearbyPreview = $source->take(3)
            ->groupBy(fn (array $row): string => $row['item']->begin_date->toDateString())
            ->map(fn ($group): array => $group->map(fn (array $row): array => ['item' => $row['item']])->all())
            ->all();

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
}
