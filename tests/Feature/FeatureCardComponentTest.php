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
        ->toContain('bg-kidical-red')
        ->toContain('rounded-card')
        ->toContain('shadow-card')
        ->toContain('aria-hidden="true"');
});

it('maps the color prop to the matching chip background utility', function () {
    $html = Blade::render(
        '<x-feature-card icon="map-pin" color="violet" title="X">body</x-feature-card>'
    );

    expect($html)
        ->toContain('bg-kidical-violet')
        ->not->toContain('bg-kidical-red');
});

it('styles an inline body link as a bold blue card link', function () {
    $html = Blade::render(<<<'BLADE'
        <x-feature-card icon="megaphone" color="red" title="X">
            Lees <a href="/visie">onze visie →</a>
        </x-feature-card>
    BLADE);

    expect($html)->toContain('onze visie');
    expect(html_entity_decode($html))
        ->toContain('[&_a]:text-kidical-blue')
        ->toContain('[&_a]:font-bold');
});

it('passes extra attributes (e.g. a page layout class) onto the card root', function () {
    $html = Blade::render(
        '<x-feature-card class="gs-expect-card" icon="ticket" title="X">body</x-feature-card>'
    );

    expect($html)->toContain('gs-expect-card');
});
