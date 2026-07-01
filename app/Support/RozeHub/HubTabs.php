<?php

namespace App\Support\RozeHub;

use App\Models\Group;

class HubTabs
{
    /**
     * Ordered hub sub-nav tabs for one chapter and the current viewer's state.
     *
     * Order rules:
     *  - base: Overzicht, Agenda, Foto's, De Groep, Materiaal
     *  - Aan de slag floats: 2nd while inside the welcome window (non-captain);
     *    for captains it sits second-to-last (just before Beheer); otherwise last.
     *  - Beheer: captains only, always last, flagged as leaving the hub (external).
     *
     * @return array<int, array{key: string, label: string, route: ?string, external: bool, active: bool}>
     */
    public static function for(Group $group, string $active, bool $isCaptain, bool $showWelcome): array
    {
        $keys = ['overzicht', 'agenda', 'fotos', 'groep', 'materiaal'];

        if ($isCaptain) {
            $keys[] = 'aan-de-slag';
        } elseif ($showWelcome) {
            array_splice($keys, 1, 0, ['aan-de-slag']);
        } else {
            $keys[] = 'aan-de-slag';
        }

        $tabs = array_map(fn (string $key) => [
            'key' => $key,
            'label' => self::LABELS[$key],
            'route' => self::ROUTES[$key],
            'external' => false,
            'active' => $key === $active,
        ], $keys);

        if ($isCaptain) {
            $tabs[] = [
                'key' => 'beheer',
                'label' => 'Beheer',
                'route' => null,
                'external' => true,
                'active' => false,
            ];
        }

        return $tabs;
    }

    private const LABELS = [
        'overzicht' => 'Overzicht',
        'aan-de-slag' => 'Aan de slag',
        'agenda' => 'Agenda',
        'fotos' => "Foto's",
        'groep' => 'De Groep',
        'materiaal' => 'Materiaal',
    ];

    private const ROUTES = [
        'overzicht' => 'groups.roze-hesjes',
        'aan-de-slag' => 'groups.roze-hesjes.aan-de-slag',
        'agenda' => 'groups.roze-hesjes.agenda',
        'fotos' => 'groups.roze-hesjes.fotos',
        'groep' => 'groups.roze-hesjes.groep',
        'materiaal' => 'groups.roze-hesjes.materiaal',
    ];
}
