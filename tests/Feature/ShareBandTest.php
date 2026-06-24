<?php

use Illuminate\Support\Facades\Blade;

it('renders the share band with framed copy and all four channels', function () {
    $url = 'https://kidicalmass.test/nl/events/17';

    $html = Blade::render(
        '<x-share-band :url="$url" title="Kidical Mass Gent" date="zondag 28 juni" />',
        ['url' => $url]
    );

    $encodedUrl = rawurlencode($url);

    expect($html)
        ->toContain('Ken je een gezin dat dit leuk zou vinden?')
        ->toContain('Kopieer link')
        ->toContain('wa.me/?text=')
        ->toContain('facebook.com/sharer/sharer.php?u='.rawurlencode($url))
        ->toContain('mailto:?subject=')
        ->toContain($encodedUrl) // the ride URL is encoded into the WhatsApp message
        ->toContain('aria-label="Deel via WhatsApp"')
        ->toContain('aria-label="Deel op Facebook"')
        ->toContain('aria-label="Deel via e-mail"');
});

it('lets callers override the heading and subline', function () {
    $html = Blade::render(
        '<x-share-band :url="$url" title="T" date="d" heading="Anders" subline="Ook anders" />',
        ['url' => 'https://example.test/x']
    );

    expect($html)->toContain('Anders')->toContain('Ook anders');
});
