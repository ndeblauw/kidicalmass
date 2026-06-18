<?php

use App\Models\Group;
use App\Support\RozeHub\HubTabs;

function tabKeys(array $tabs): array
{
    return array_map(fn (array $t) => $t['key'], $tabs);
}

test('new hesje (welcome on) puts Aan de slag second', function () {
    $tabs = HubTabs::for(new Group, 'overzicht', isCaptain: false, showWelcome: true);

    expect(tabKeys($tabs))->toBe([
        'overzicht', 'aan-de-slag', 'agenda', 'fotos', 'groep', 'materiaal',
    ]);
});

test('established hesje (welcome off) puts Aan de slag last, no Beheer', function () {
    $tabs = HubTabs::for(new Group, 'overzicht', isCaptain: false, showWelcome: false);

    expect(tabKeys($tabs))->toBe([
        'overzicht', 'agenda', 'fotos', 'groep', 'materiaal', 'aan-de-slag',
    ]);
});

test('captain gets Aan de slag second-to-last and Beheer last (external)', function () {
    $tabs = HubTabs::for(new Group, 'agenda', isCaptain: true, showWelcome: false);

    expect(tabKeys($tabs))->toBe([
        'overzicht', 'agenda', 'fotos', 'groep', 'materiaal', 'aan-de-slag', 'beheer',
    ]);

    $beheer = end($tabs);
    expect($beheer['external'])->toBeTrue();
    expect($beheer['route'])->toBeNull();
});

test('the active key is flagged on exactly one tab', function () {
    $tabs = HubTabs::for(new Group, 'agenda', isCaptain: false, showWelcome: false);

    $active = array_values(array_filter($tabs, fn (array $t) => $t['active']));
    expect($active)->toHaveCount(1);
    expect($active[0]['key'])->toBe('agenda');
});
