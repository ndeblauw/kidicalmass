<?php

use Illuminate\Support\Facades\Blade;

it('shows the eyebrow, title, illustration, controls and body content', function () {
    $html = Blade::render(<<<'BLADE'
        <x-page-hero
            eyebrow="Kalender"
            title="Spring op de fiets, wij rijden samen."
            illustration="img/illustrations/kid-on-bike.png">
            <x-slot:controls><div class="probe-control">picker</div></x-slot:controls>
            <p class="probe-body">page body</p>
        </x-page-hero>
    BLADE);

    expect($html)
        ->toContain('Kalender')
        ->toContain('Spring op de fiets, wij rijden samen.')
        ->toContain('kid-on-bike.png')
        ->toContain('aria-hidden="true"')
        ->toContain('probe-control')
        ->toContain('probe-body')
        ->toContain('page-hero')
        ->toContain('page-panel')
        // Artwork present, so the copy keeps its width caps.
        ->not->toContain('page-hero--bare');
});

it('omits the illustration and goes bare (full-width copy) when none is given', function () {
    $html = Blade::render(<<<'BLADE'
        <x-page-hero eyebrow="Meehelpen" title="Jouw handen maken de stoet.">
            <p>body</p>
        </x-page-hero>
    BLADE);

    expect($html)
        ->not->toContain('page-hero__visual')
        ->toContain('page-hero--bare');
});

it('requires a photo alt decision when a photo is set', function () {
    expect(fn () => Blade::render(
        '<x-page-hero eyebrow="Test" title="Test" photo="img/photography/ride-crowd-intersection.webp" />'
    ))->toThrow(Exception::class, 'photo-alt');
});
