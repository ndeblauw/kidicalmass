<?php

namespace App\Support;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\YearStat;
use Illuminate\Support\Number;

/**
 * The raw impact numbers behind every public stat deck (Home, /steun-ons,
 * About): one definition per figure, so the decks cannot drift apart. The
 * decks ({@see SupportStats}, {@see AboutStats}, {@see MovementStats}) only
 * choose which figures to show, with which label and colour.
 *
 * Year-bound figures hang off one reference year: the most recent curated
 * {@see YearStat}, or the last completed calendar year as a fallback. The
 * curated figures (participants, volunteers) come from that same row.
 */
class ImpactFigures
{
    private ?int $referenceYear = null;

    private ?YearStat $referenceYearStat = null;

    private bool $referenceYearStatLoaded = false;

    /** Live count of visible local groups. */
    public function visibleGroups(): int
    {
        return Group::visible()->count();
    }

    /**
     * The most recent year we have a curated row for, falling back to the last
     * completed calendar year so the rides figure still has a year to count.
     */
    public function referenceYear(): int
    {
        return $this->referenceYear ??= YearStat::max('year') ?? now()->subYear()->year;
    }

    /**
     * The curated row for the reference year (participants, volunteers), or
     * null when nobody has entered one yet.
     */
    public function referenceYearStat(): ?YearStat
    {
        if (! $this->referenceYearStatLoaded) {
            $this->referenceYearStat = YearStat::where('year', $this->referenceYear())->first();
            $this->referenceYearStatLoaded = true;
        }

        return $this->referenceYearStat;
    }

    /** Published parades (KIDICALMASS rides): held in the given year, or all-time when null. */
    public function rides(?int $year = null): int
    {
        return Activity::query()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->published()
            ->when($year !== null, fn ($query) => $query
                ->where('begin_date', '>=', sprintf('%04d-01-01 00:00:00', $year))
                ->where('begin_date', '<', sprintf('%04d-01-01 00:00:00', $year + 1)))
            ->count();
    }

    /** Curated participant count for the reference year; null when nobody entered one. */
    public function participants(): ?int
    {
        return $this->referenceYearStat()?->participants;
    }

    /** Localised number formatting: 5500 -> "5.500" under nl. */
    public static function format(int $value): string
    {
        return Number::format($value, locale: app()->getLocale());
    }
}
