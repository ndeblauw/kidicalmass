<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Single source of truth for how a ride's date and time render across the site.
 * Locale-aware (see SetLocale::SUPPORTED); output is always lowercase — casing
 * is a CSS concern.
 */
class RideDate
{
    /**
     * Ride dates are stored and shown as Belgian wall-clock time; the app
     * timezone (UTC) only labels them.
     */
    public const TIMEZONE = 'Europe/Brussels';

    /**
     * Midnight at the start of today in Belgium, as wall-clock time in the app
     * timezone so it compares directly with stored ride dates. Between 00:00
     * and 02:00 Belgian time this is already the new day, where a UTC
     * startOfDay() would still be yesterday.
     */
    public static function startOfToday(): CarbonInterface
    {
        return now(self::TIMEZONE)->startOfDay()->shiftTimezone(config('app.timezone'));
    }

    /**
     * Belgian time: "14u" / "14u30" (nl), "14h" / "14h30" (fr). The separator
     * comes from `common.time_separator`; whole hours drop the minutes.
     */
    public static function time(Carbon|string $date): string
    {
        $carbon = self::resolve($date);
        $separator = __('common.time_separator');
        $minutes = $carbon->format('i');

        return $carbon->format('G').$separator.($minutes === '00' ? '' : $minutes);
    }

    /** Abbreviated date for dense rows: "zo 14 jun." / "di 14 juin". */
    public static function short(Carbon|string $date): string
    {
        return self::localized($date)->isoFormat('dd D MMM');
    }

    /** Spelled-out date for prose/heroes: "zondag 14 juni" / "dimanche 14 juin". */
    public static function full(Carbon|string $date): string
    {
        return self::localized($date)->isoFormat('dddd D MMMM');
    }

    /** Weekday name only: "zondag" / "dimanche". Lowercase — casing is a CSS concern. */
    public static function weekday(Carbon|string $date): string
    {
        return self::localized($date)->isoFormat('dddd');
    }

    /** Month grouping header: "juni 2026" / "juin 2026". */
    public static function monthYear(Carbon|string $date): string
    {
        return self::localized($date)->isoFormat('MMMM YYYY');
    }

    /**
     * Parts for the calendar-page lockup: big date number and a 3-letter month, plus a
     * stable readable tilt. Rendered in the order num -> month; the weekday now rides in
     * the row's meta line (see RideDate::weekday()), not the lockup.
     *
     * @return array{num: string, month: string, rotation: float}
     */
    public static function rail(Carbon|string $date): array
    {
        $carbon = self::localized($date);

        return [
            'num' => $carbon->isoFormat('D'),
            'month' => rtrim($carbon->isoFormat('MMM'), '.'),
            'rotation' => self::railRotation($date),
        ];
    }

    /**
     * Deterministic readable tilt for the calendar lockup: stable per calendar date
     * (seeded from the date string, locale-independent), in the range [-5, 5] degrees.
     */
    public static function railRotation(Carbon|string $date): float
    {
        $seed = crc32(self::resolve($date)->toDateString());

        return round(($seed % 1001) / 1000 * 10 - 5, 2);
    }

    private static function resolve(Carbon|string $date): Carbon
    {
        return $date instanceof Carbon ? $date : Carbon::parse($date);
    }

    private static function localized(Carbon|string $date): Carbon
    {
        return self::resolve($date)->copy()->locale(app()->getLocale());
    }
}
