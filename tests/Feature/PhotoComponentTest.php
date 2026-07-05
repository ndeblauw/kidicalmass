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

it('emits intrinsic width and height so the browser reserves space', function () {
    // ride-crowd-intersection.webp is 1600×1200; without these attributes the
    // page reflows (CLS) when each lazy photo arrives.
    $html = Blade::render(
        '<x-photo src="img/photography/ride-crowd-intersection.webp" alt="Een rit" />'
    );

    expect($html)
        ->toContain('width="1600"')
        ->toContain('height="1200"');
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

    // `photo` is the component's identity hook (components/photo.css); passed
    // classes compose with it rather than replacing it.
    expect($html)
        ->toContain('class="photo hero"')
        ->toContain('loading="eager"')
        ->toContain('fetchpriority="high"');
});

it('requires an explicit alt decision', function () {
    // A forgotten alt must fail loudly, not silently ship an undescribed photo.
    // Decorative photos pass alt="" on purpose.
    expect(fn () => Blade::render(
        '<x-photo src="img/photography/ride-crowd-intersection.webp" />'
    ))->toThrow(Exception::class, 'alt');
});
