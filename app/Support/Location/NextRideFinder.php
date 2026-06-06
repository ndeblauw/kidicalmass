<?php

namespace App\Support\Location;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;

class NextRideFinder
{
    /**
     * Resolve the single ride to feature on the homepage's "De volgende rit bij jou".
     * Prefers the soonest ride within the nearby radius; otherwise the soonest ride
     * anywhere (flagged far). Returns ride=null when no location is set (picker state)
     * or when there are no upcoming rides at all (off-season).
     *
     * @param  array{zip: string, lat: float, lng: float, name: string}|null  $location
     * @return array{ride: Activity|null, distance_km: float|null, is_far: bool, has_upcoming: bool}
     */
    public static function find(?array $location): array
    {
        $upcoming = Activity::query()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->where('begin_date', '>=', now()->startOfDay())
            ->orderBy('begin_date')
            ->get();

        if ($upcoming->isEmpty()) {
            return ['ride' => null, 'distance_km' => null, 'is_far' => false, 'has_upcoming' => false];
        }

        if (! $location) {
            return ['ride' => null, 'distance_km' => null, 'is_far' => false, 'has_upcoming' => true];
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

        $chosen = $partition['nearby']->first() ?? $partition['far']->first();

        return [
            'ride' => $chosen['item'],
            'distance_km' => $chosen['distance_km'],
            // A ride whose postal_code can't be resolved to coordinates falls into the
            // "far" bucket (distance_km null), so it is reported as far rather than hidden.
            'is_far' => $partition['nearby']->isEmpty(),
            'has_upcoming' => true,
        ];
    }
}
