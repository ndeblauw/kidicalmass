<?php

use Illuminate\Support\Facades\Blade;

it('emits a two-step srcset when a 768px sibling exists and the original is wider', function () {
    // ride-crowd-intersection.webp is 1600px wide and ships a -768 sibling.
    $html = Blade::render(
        '<x-photo src="img/photography/ride-crowd-intersection.webp" alt="Een rit" />'
    );

    expect($html)
        ->toContain('ride-crowd-intersection-768.webp')
        ->toContain('768w')
        ->toContain('1600w')
        ->toContain('sizes=')
        ->toContain('alt="Een rit"')
        ->toContain('loading="lazy"')
        ->toContain('decoding="async"');
});

it('omits srcset when no 768px sibling exists', function () {
    // SVG illustration: no responsive variant, no srcset.
    $html = Blade::render(
        '<x-photo src="img/illustrations/waving-rider.svg" alt="" />'
    );

    expect($html)
        ->toContain('waving-rider.svg')
        ->not->toContain('srcset');
});

it('passes a class through and supports an eager fetchpriority hint', function () {
    $html = Blade::render(
        '<x-photo src="img/photography/ride-crowd-intersection.webp" alt="x" class="hero" loading="eager" fetchpriority="high" />'
    );

    expect($html)
        ->toContain('class="hero"')
        ->toContain('loading="eager"')
        ->toContain('fetchpriority="high"');
});
