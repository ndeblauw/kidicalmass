<?php

namespace App\Support;

use App\Models\YearStat;

/**
 * The About-section impact numbers: one deck for "Wat we doen" and the two
 * highlights beside the About hub intro. Counts what the database knows
 * (visible groups, all-time published parades) and reads what only humans know
 * (participants, volunteers) from the reference year's curated {@see YearStat}
 * row. Every figure comes from {@see ImpactFigures}, shared with Home and
 * /steun-ons; this class only picks labels and colours. A metric without an
 * honest value yields no card, mirroring {@see SupportStats}.
 */
class AboutStats
{
    public function __construct(private ImpactFigures $figures = new ImpactFigures) {}

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
        return $this->card($this->figures->visibleGroups(), __('about.stats.groups'), $color);
    }

    /** @return array{value: string, label: string, color: string}|null */
    private function rides(string $color): ?array
    {
        $rides = $this->figures->rides();

        return $rides > 0 ? $this->card($rides, __('about.stats.rides'), $color) : null;
    }

    /** @return array{value: string, label: string, color: string}|null */
    private function volunteers(string $color): ?array
    {
        $latest = $this->figures->referenceYearStat();

        return $latest?->volunteers ? $this->card($latest->volunteers, __('about.stats.volunteers'), $color) : null;
    }

    /** @return array{value: string, label: string, color: string}|null */
    private function participants(string $color): ?array
    {
        $latest = $this->figures->referenceYearStat();

        return $latest?->participants
            ? $this->card($latest->participants, __('about.stats.participants', ['year' => $latest->year]), $color)
            : null;
    }

    /** @return array{value: string, label: string, color: string} */
    private function card(int $value, string $label, string $color): array
    {
        return ['value' => ImpactFigures::format($value), 'label' => $label, 'color' => $color];
    }
}
