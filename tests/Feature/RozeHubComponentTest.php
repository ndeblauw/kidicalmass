<?php

use App\Models\Group;
use Illuminate\Support\Facades\Blade;

test('the site layout renders a supplied navbar slot instead of the marketing header', function () {
    $html = Blade::render(
        '<x-layouts::site><x-slot:navbar>SHELLBAR</x-slot:navbar>BODY</x-layouts::site>',
    );

    expect($html)
        ->toContain('SHELLBAR')
        ->toContain('BODY')
        ->not->toContain('site-nav__links');
});

test('the site layout falls back to the marketing header with no navbar slot', function () {
    $html = Blade::render('<x-layouts::site>BODY</x-layouts::site>');

    expect($html)
        ->toContain('site-nav__links')
        ->toContain('BODY');
});

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
