<?php

use Illuminate\Support\Facades\Blade;

it('intro-text renders slot content with the intro-text class', function () {
    $html = Blade::render('<x-intro-text>Paragraph here.</x-intro-text>');

    expect($html)
        ->toContain('class="intro-text"')
        ->toContain('Paragraph here.');
});

it('intro-text lead variant adds the lead modifier class', function () {
    $html = Blade::render('<x-intro-text size="lead">Big lead.</x-intro-text>');

    expect($html)->toContain('intro-text--lead');
});

it('section-heading renders as h2 by default with the section-heading class', function () {
    $html = Blade::render('<x-section-heading>Wie wat doet</x-section-heading>');

    expect($html)
        ->toContain('<h2')
        ->toContain('class="section-heading"')
        ->toContain('Wie wat doet')
        ->toContain('</h2>');
});

it('section-heading respects the as prop to render a different heading level', function () {
    $html = Blade::render('<x-section-heading as="h3">Subkop</x-section-heading>');

    expect($html)->toContain('<h3')->toContain('</h3>');
});
