<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Single source of truth for how a ride's date and time render across the site.
 * Locale-aware (nl/fr); output is always lowercase — casing is a CSS concern.
 */
class RideDate
{
    /** Belgian time: "14u" / "14u30" (nl), "14h" / "14h30" (fr). Whole hours drop the minutes. */
    public static function time(Carbon|string $date): string
    {
        $carbon = self::resolve($date);
        $separator = app()->getLocale() === 'fr' ? 'h' : 'u';
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

    /** Month grouping header: "juni 2026" / "juin 2026". */
    public static function monthYear(Carbon|string $date): string
    {
        return self::localized($date)->isoFormat('MMMM YYYY');
    }

    /**
     * Parts for the calendar-page lockup: full day name, date number, month, plus a
     * stable readable tilt. Rendered in the order day -> num -> month.
     *
     * @return array{num: string, month: string, day: string, rotation: float}
     */
    public static function rail(Carbon|string $date): array
    {
        $carbon = self::localized($date);

        return [
            'num' => $carbon->isoFormat('D'),
            'month' => $carbon->isoFormat('MMMM'),
            'day' => $carbon->isoFormat('dddd'),
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
