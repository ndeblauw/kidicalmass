<?php

use App\Models\Group;
use Illuminate\Support\Facades\Blade;

test('the shell bar links the logo back to the public home', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek']);

    $html = Blade::render('<x-roze-shell-bar :group="$group" />', ['group' => $group]);

    expect($html)
        ->toContain('roze-shell-bar')
        ->toContain('href="'.route('home').'"');
});

test('the shell bar shows the chapter name and the roze-hesje role', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek']);

    $html = Blade::render('<x-roze-shell-bar :group="$group" />', ['group' => $group]);

    expect($html)
        ->toContain('Schaarbeek')
        ->toContain('roze hesjes')
        ->not->toContain('Kidical Mass Schaarbeek'); // brand carried by the logo, not the label
});

test('the shell bar shows a plain label (no switcher) when no one is signed in', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek']);

    $html = Blade::render('<x-roze-shell-bar :group="$group" />', ['group' => $group]);

    expect($html)
        ->toContain('roze-shell-bar__context')
        ->not->toContain('roze-shell-switch');
});
