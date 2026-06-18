<?php

use App\Models\Group;
use Illuminate\Support\Facades\Blade;

test('roze-hub renders the chapter name in the compact hero', function () {
    $group = Group::factory()->create(['name' => 'Schaarbeek']);

    $html = Blade::render(
        '<x-roze-hub :group="$group" active="overzicht" :is-captain="false" :show-welcome="false">BODY</x-roze-hub>',
        ['group' => $group],
    );

    expect($html)
        ->toContain('roze-hub-hero')
        ->toContain('Kidical Mass Schaarbeek')
        ->toContain('BODY');
});

test('the active tab carries the active modifier class', function () {
    $group = Group::factory()->create(['name' => 'Schaarbeek']);

    $html = Blade::render(
        '<x-roze-hub :group="$group" active="agenda" :is-captain="false" :show-welcome="false">x</x-roze-hub>',
        ['group' => $group],
    );

    expect($html)->toContain('roze-subnav__tab--active');
});

test('Beheer appears only for captains', function () {
    $group = Group::factory()->create(['name' => 'Schaarbeek']);

    $captainHtml = Blade::render(
        '<x-roze-hub :group="$group" active="overzicht" :is-captain="true" :show-welcome="false" beheer-url="/admin">x</x-roze-hub>',
        ['group' => $group],
    );
    $memberHtml = Blade::render(
        '<x-roze-hub :group="$group" active="overzicht" :is-captain="false" :show-welcome="false">x</x-roze-hub>',
        ['group' => $group],
    );

    expect($captainHtml)->toContain('roze-subnav__beheer');
    expect($memberHtml)->not->toContain('roze-subnav__beheer');
});
