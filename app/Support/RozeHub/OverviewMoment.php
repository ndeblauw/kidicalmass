<?php

namespace App\Support\RozeHub;

use App\Models\Activity;

/**
 * Which single "moment" leads the hub Overview. The page picks exactly one
 * (welcome > recap > pre-ride > default) so busy weeks stay calm: one lead
 * moment, everything else in its normal section. Greeting strings live in the
 * Blade layer (overzicht.blade.php); this class only decides and counts.
 */
class OverviewMoment
{
    /** Recap leads while the last ride (with photos) is at most this many days old. */
    public const RECAP_DAYS = 5;

    /** Pre-ride greeting + countdown appear within this many nights of the next ride. */
    public const PRE_RIDE_DAYS = 7;

    /** @return 'welcome'|'recap'|'pre-ride'|'default' */
    public static function resolve(bool $showWelcome, ?Activity $recapRide, ?Activity $nextRide): string
    {
        if ($showWelcome) {
            return 'welcome';
        }

        if ($recapRide !== null) {
            return 'recap';
        }

        if ($nextRide !== null && $nextRide->activity_type->isRide() && self::nightsUntil($nextRide) <= self::PRE_RIDE_DAYS) {
            return 'pre-ride';
        }

        return 'default';
    }

    /**
     * The playful countdown for the next-ride card. Rides only: "nachtjes slapen"
     * before a vergadering would be odd, so meetings render no line at all.
     */
    public static function countdownLabel(Activity $nextRide): ?string
    {
        if (! $nextRide->activity_type->isRide()) {
            return null;
        }

        $nights = self::nightsUntil($nextRide);

        return match (true) {
            $nights === 0 => 'Vandaag rijden we!',
            $nights === 1 => 'Morgen is het zover.',
            $nights <= self::PRE_RIDE_DAYS => "Nog {$nights} nachtjes slapen.",
            default => null,
        };
    }

    /** Whole nights between today and the ride day (0 on the day itself). */
    private static function nightsUntil(Activity $ride): int
    {
        return (int) now()->startOfDay()->diffInDays($ride->begin_date->copy()->startOfDay());
    }
}
