<?php

namespace App\Support;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\YearStat;
use Illuminate\Support\Number;

/**
 * The About-section impact numbers: one source of truth for the full deck on
 * "Wat we doen" and the two highlights beside the About hub intro. Counts what
 * the database knows (visible groups, all-time published parades) and reads
 * what only humans know (participants, volunteers) from the latest curated
 * {@see YearStat} row. A metric without an honest value yields no card,
 * mirroring {@see SupportStats}.
 */
class AboutStats
{
    private ?YearStat $latestYear = null;

    private bool $latestYearLoaded = false;

    /**
     * The full deck (mission page).
     *
     * @return array<int, array{value: string, label: string, color: string}>
     */
    public function cards(): array
    {
        /* Colour order blue, red, green, red: adjacent cards always differ and
           the deck never bookends the same colour (polish 2026-07-04). */
        return array_values(array_filter([
            $this->groups('blue'),
            $this->rides('red'),
            $this->volunteers('green'),
            $this->participants('red'),
        ]));
    }

    /**
     * Two headline numbers for the About hub (meeting 2026-09-22): the network's
     * size plus the curated reach of the latest year. Participants fall back to
     * volunteers when the Jaarcijfers row has none.
     *
     * @return array<int, array{value: string, label: string, color: string}>
     */
    public function highlights(): array
    {
        return array_values(array_filter([
            $this->groups('blue'),
            $this->participants('red') ?? $this->volunteers('red'),
        ]));
    }

    /** @return array{value: string, label: string, color: string} */
    private function groups(string $color): array
    {
        return $this->card(Group::visible()->count(), __('about.stats.groups'), $color);
    }

    /** @return array{value: string, label: string, color: string}|null */
    private function rides(string $color): ?array
    {
        $rides = Activity::query()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->published()
            ->count();

        return $rides > 0 ? $this->card($rides, __('about.stats.rides'), $color) : null;
    }

    /** @return array{value: string, label: string, color: string}|null */
    private function volunteers(string $color): ?array
    {
        $latest = $this->latestYear();

        return $latest?->volunteers ? $this->card($latest->volunteers, __('about.stats.volunteers'), $color) : null;
    }

    /** @return array{value: string, label: string, color: string}|null */
    private function participants(string $color): ?array
    {
        $latest = $this->latestYear();

        return $latest?->participants
            ? $this->card($latest->participants, __('about.stats.participants', ['year' => $latest->year]), $color)
            : null;
    }

    private function latestYear(): ?YearStat
    {
        if (! $this->latestYearLoaded) {
            $this->latestYear = YearStat::query()->orderByDesc('year')->first();
            $this->latestYearLoaded = true;
        }

        return $this->latestYear;
    }

    /** @return array{value: string, label: string, color: string} */
    private function card(int $value, string $label, string $color): array
    {
        return ['value' => $this->format($value), 'label' => $label, 'color' => $color];
    }

    /** Localised number formatting: 5500 -> "5.500" under nl. */
    private function format(int $value): string
    {
        return Number::format($value, locale: app()->getLocale());
    }
}
