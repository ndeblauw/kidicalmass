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
