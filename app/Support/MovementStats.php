<?php

namespace App\Support;

/**
 * The typographic stat row in the Home "Eén beweging" beat: local groups,
 * parades in the reference year, participants in the reference year. Same
 * figures as the /steun-ons deck ({@see ImpactFigures}), Home's own labels and
 * colours. A figure without a real value (no rides, no curated participant
 * count) is dropped, never shown as "0".
 */
class MovementStats
{
    public function __construct(private ImpactFigures $figures = new ImpactFigures) {}

    /**
     * @return list<array{key: string, value: string, label: string, color: string}>
     */
    public function items(): array
    {
        $year = $this->figures->referenceYear();

        return array_values(array_filter([
            $this->item('groups', $this->figures->visibleGroups(), __('home.routes.movement.stats.groups'), 'blue'),
            $this->item('rides', $this->figures->rides($year), __('home.routes.movement.stats.rides', ['year' => $year]), 'green'),
            $this->item('participants', $this->figures->participants(), __('home.routes.movement.stats.participants', ['year' => $year]), 'red'),
        ]));
    }

    /** @return array{key: string, value: string, label: string, color: string}|null */
    private function item(string $key, ?int $value, string $label, string $color): ?array
    {
        if (! $value) {
            return null;
        }

        return ['key' => $key, 'value' => ImpactFigures::format($value), 'label' => $label, 'color' => $color];
    }
}
