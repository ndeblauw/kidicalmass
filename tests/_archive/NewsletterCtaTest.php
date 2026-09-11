<?php

use Illuminate\Support\Facades\Blade;

it('renders the teaser on a yellow band with default copy and a CTA to the signup page', function () {
    $html = Blade::render('<x-newsletter-cta />');

    expect($html)
        ->toContain('Krijg de nieuwste ritten in je mailbox')
        ->toContain('bg-kidical-yellow')
        ->toContain('Schrijf me in')
        ->toContain('nieuwsbrief')
        ->not->toContain('href="#"');
});

it('renders a three-chip fleet straddling the seam, one per brand colour', function () {
    $html = Blade::render('<x-newsletter-cta />');

    expect($html)
        ->toContain('newsletter-cta__chip--green')
        ->toContain('newsletter-cta__chip--red')
        ->toContain('newsletter-cta__chip--blue');

    expect(substr_count($html, 'newsletter-cta__chip newsletter-cta__chip--'))->toBe(3);
});

it('arms the scroll-into-view reveal so the chips animate in', function () {
    expect(Blade::render('<x-newsletter-cta />'))
        ->toContain('is-ready')
        ->toContain('is-inview')
        ->toContain('IntersectionObserver');
});

it('lets the heading and lead be overridden', function () {
    $html = Blade::render('<x-newsletter-cta heading="Blijf op de hoogte" lead="Eén mail per maand." />');

    expect($html)
        ->toContain('Blijf op de hoogte')
        ->toContain('Eén mail per maand.');
});
