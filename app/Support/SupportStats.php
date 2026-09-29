<?php

namespace App\Support;

/**
 * Builds the proof-of-impact deck shown on /steun-ons. Three honest metrics:
 *  - lokale groepen  — live count of visible local groups,
 *  - ritten in <jaar> — published rides held in the reference year,
 *  - deelnemers       — a curated per-year figure (no attendance tracking exists).
 *
 * The numbers come from {@see ImpactFigures} (shared with Home and About); this
 * class only picks labels and colours. A card is only emitted when it has a
 * real value: an empty year never shows a misleading "0".
 */
class SupportStats
{
    public function __construct(private ImpactFigures $figures = new ImpactFigures) {}

    /**
     * @return array<int, array{value: string, label: string, color: string}>
     */
    public function cards(): array
    {
        $year = $this->referenceYear();

        $cards = [];

        // Bottom of the stacked deck up to the legible top card.
        $cards[] = [
            'value' => ImpactFigures::format($this->figures->visibleGroups()),
            'label' => __('support.stats.groups'),
            'color' => 'red',
        ];

        $rides = $this->figures->rides($year);
        if ($rides > 0) {
            $cards[] = [
                'value' => ImpactFigures::format($rides),
                'label' => __('support.stats.rides', ['year' => $year]),
                'color' => 'green',
            ];
        }

        $participants = $this->figures->participants();
        if ($participants !== null) {
            $cards[] = [
                'value' => ImpactFigures::format($participants),
                'label' => __('support.stats.participants', ['year' => $year]),
                'color' => 'blue',
            ];
        }

        return $cards;
    }

    /** @see ImpactFigures::referenceYear() */
    public function referenceYear(): int
    {
        return $this->figures->referenceYear();
    }
}
