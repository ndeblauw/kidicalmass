<?php

use Illuminate\Support\Facades\Blade;

it('renders a yellow arrow button by default', function () {
    $html = Blade::render(<<<'BLADE'
        <x-cta-button href="/rides">Find a ride</x-cta-button>
    BLADE);

    expect($html)
        ->toContain('href="/rides"')
        ->toContain('Find a ride')
        ->toContain('cta-button')
        ->toContain('cta-button--yellow')
        ->toContain('cta-button__slot--left')
        ->toContain('cta-button__slot--right')
        ->toContain('cta-button__label')
        // default disc glyph is the arrow, not the heart
        ->toContain('M2 7h10')
        ->not->toContain('cta-button--blue')
        ->not->toContain('cta-button--sm');
});

it('applies the blue variant on yellow grounds', function () {
    $html = Blade::render(<<<'BLADE'
        <x-cta-button href="/rides" variant="blue">Vind een rit</x-cta-button>
    BLADE);

    expect($html)
        ->toContain('cta-button--blue')
        ->not->toContain('cta-button--yellow');
});

it('renders the small size with a heart disc for support asks', function () {
    $html = Blade::render(<<<'BLADE'
        <x-cta-button href="/steun" icon="heart" size="sm">Steun ons</x-cta-button>
    BLADE);

    expect($html)
        ->toContain('cta-button--sm')
        // heart path, arrow path absent
        ->toContain('11.645 20.91')
        ->not->toContain('M2 7h10');
});

it('merges extra classes and passes through attributes', function () {
    $html = Blade::render(<<<'BLADE'
        <x-cta-button href="/x" class="cta-button--block" target="_blank">Go</x-cta-button>
    BLADE);

    expect($html)
        ->toContain('cta-button--block')
        ->toContain('cta-button') // base class still present after merge
        ->toContain('target="_blank"');
});
