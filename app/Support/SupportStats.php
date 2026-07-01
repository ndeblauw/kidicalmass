<?php

namespace App\Support;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\YearStat;
use Illuminate\Support\Number;

/**
 * Builds the proof-of-impact deck shown on /steun-ons. Three honest metrics:
 *  - lokale groepen  — live count of visible local groups,
 *  - ritten in <jaar> — published rides held in the reference year,
 *  - deelnemers       — a curated per-year figure (no attendance tracking exists).
 *
 * Everything hangs off one reference year (the most recent {@see YearStat}, or
 * the last completed calendar year as a fallback) so both year-bound cards stay
 * in sync. A card is only emitted when it has a real value: an empty year never
 * shows a misleading "0".
 */
class SupportStats
{
    /**
     * @return array<int, array{value: string, label: string, color: string}>
     */
    public function cards(): array
    {
        $year = $this->referenceYear();

        $cards = [];

        // Bottom of the stacked deck up to the legible top card.
        $cards[] = [
            'value' => $this->format(Group::visible()->count()),
            'label' => __('support.stat_groups'),
            'color' => 'red',
        ];

        $rides = $this->rideCount($year);
        if ($rides > 0) {
            $cards[] = [
                'value' => $this->format($rides),
                'label' => __('support.stat_rides', ['year' => $year]),
                'color' => 'green',
            ];
        }

        $participants = $this->participantCount($year);
        if ($participants !== null) {
            $cards[] = [
                'value' => $this->format($participants),
                'label' => __('support.stat_participants', ['year' => $year]),
                'color' => 'blue',
            ];
        }

        return $cards;
    }

    /**
     * The most recent year we have a curated row for, falling back to the last
     * completed calendar year so the rides card still has a year to count.
     */
    public function referenceYear(): int
    {
        return YearStat::max('year') ?? now()->subYear()->year;
    }

    private function rideCount(int $year): int
    {
        return Activity::query()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->published()
            ->whereYear('begin_date', $year)
            ->count();
    }

    private function participantCount(int $year): ?int
    {
        return YearStat::where('year', $year)->value('participants');
    }

    /** Localised number formatting: 5500 -> "5.500" under nl. */
    private function format(int $value): string
    {
        return Number::format($value, locale: app()->getLocale());
    }
}
