<?php

use Illuminate\Support\Facades\Blade;

it('renders the newsletter sign-up on a yellow band with the default copy', function () {
    $html = Blade::render('<x-newsletter-cta />');

    expect($html)
        ->toContain('Elke maand de nieuwste ritten in je bus')
        ->toContain('bg-kidical-yellow')
        ->toContain('newsletter-cta__input')
        ->toContain('Schrijf me in');
});

it('renders a three-chip fleet straddling the seam, one per brand colour', function () {
    $html = Blade::render('<x-newsletter-cta />');

    expect($html)
        ->toContain('newsletter-cta__chips')
        ->toContain('newsletter-cta__chip--green')
        ->toContain('newsletter-cta__chip--red')
        ->toContain('newsletter-cta__chip--blue');

    expect(substr_count($html, 'newsletter-cta__chip newsletter-cta__chip--'))->toBe(3);
});

it('arms the scroll-into-view reveal so the chips animate in', function () {
    $html = Blade::render('<x-newsletter-cta />');

    expect($html)
        ->toContain('is-ready')
        ->toContain('is-inview')
        ->toContain('IntersectionObserver');
});

it('lets the heading, lead and note be overridden', function () {
    $html = Blade::render('<x-newsletter-cta heading="Blijf op de hoogte" lead="Eén mail per maand." note="Altijd uitschrijfbaar." />');

    expect($html)
        ->toContain('Blijf op de hoogte')
        ->toContain('Eén mail per maand.')
        ->toContain('Altijd uitschrijfbaar.');
});
