<?php

namespace App\Support;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\YearStat;
use Illuminate\Support\Number;

/**
 * The About-section impact deck: one source of truth for the numbers on the
 * About hub and the "Wat we doen" page. Counts what the database knows (visible
 * groups, all-time published parades) and reads what only humans know
 * (participants, volunteers) from the latest curated {@see YearStat} row.
 * A metric without an honest value yields no card, mirroring {@see SupportStats}.
 */
class AboutStats
{
    /**
     * @return array<int, array{value: string, label: string, color: string}>
     */
    public function cards(): array
    {
        /* Colour order blue, red, green, red: adjacent cards always differ and
           the deck never bookends the same colour (polish 2026-07-04). */
        $cards = [[
            'value' => $this->format(Group::visible()->count()),
            'label' => __('about.stat_groups'),
            'color' => 'blue',
        ]];

        $rides = Activity::query()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->published()
            ->count();

        if ($rides > 0) {
            $cards[] = [
                'value' => $this->format($rides),
                'label' => __('about.stat_rides'),
                'color' => 'red',
            ];
        }

        $latest = YearStat::query()->orderByDesc('year')->first();

        if ($latest?->volunteers) {
            $cards[] = [
                'value' => $this->format($latest->volunteers),
                'label' => __('about.stat_volunteers'),
                'color' => 'green',
            ];
        }

        if ($latest?->participants) {
            $cards[] = [
                'value' => $this->format($latest->participants),
                'label' => __('about.stat_participants', ['year' => $latest->year]),
                'color' => 'red',
            ];
        }

        return $cards;
    }

    /** Localised number formatting: 5500 -> "5.500" under nl. */
    private function format(int $value): string
    {
        return Number::format($value, locale: app()->getLocale());
    }
}
