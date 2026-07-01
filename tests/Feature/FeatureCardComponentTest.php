<?php

use Illuminate\Support\Facades\Blade;

it('renders the chip icon, title and body with the default red chip', function () {
    $html = Blade::render(<<<'BLADE'
        <x-feature-card icon="clock" title="Kort en rustig">
            5 à 7 km op het tempo van het jongste kind.
        </x-feature-card>
    BLADE);

    expect($html)
        ->toContain('Kort en rustig')
        ->toContain('5 à 7 km op het tempo')
        ->toContain('data-icon-chip')        // the chip renders
        ->toContain('data-color="red"')      // default chip colour
        ->toContain('aria-hidden="true"');   // the icon is decorative
});

it('forwards the color prop to the chip', function () {
    $html = Blade::render(
        '<x-feature-card icon="map-pin" color="violet" title="X">body</x-feature-card>'
    );

    expect($html)
        ->toContain('data-color="violet"')
        ->not->toContain('data-color="red"');
});

it('renders an inline body link with its href', function () {
    $html = Blade::render(<<<'BLADE'
        <x-feature-card icon="megaphone" color="red" title="X">
            Lees <a href="/visie">onze visie →</a>
        </x-feature-card>
    BLADE);

    expect($html)
        ->toContain('onze visie')
        ->toContain('href="/visie"');
});

it('passes extra attributes (e.g. a page layout class) onto the card root', function () {
    $html = Blade::render(
        '<x-feature-card class="gs-expect-card" icon="ticket" title="X">body</x-feature-card>'
    );

    expect($html)->toContain('gs-expect-card');
});

it('renders the compact md size with the roze-card title face', function () {
    $html = Blade::render(
        '<x-feature-card icon="users" title="X" size="md">body</x-feature-card>'
    );

    expect($html)
        ->toContain('data-size="md"')      // the compact variant
        ->toContain('roze-card-title')     // compact title face
        ->not->toContain('feature-card');  // compact drops the full-size identity hook
});

it('roze-card is a thin alias for the compact feature-card', function () {
    $html = Blade::render(
        '<x-roze-card icon="users" title="Je rijdt mee" color="orange">samen op pad</x-roze-card>'
    );

    expect($html)
        ->toContain('Je rijdt mee')
        ->toContain('samen op pad')
        ->toContain('roze-card-title')
        ->toContain('data-size="md"')        // renders the compact size
        ->toContain('data-color="orange"');  // forwards its colour to the chip
});
