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

it('pull-quote large renders blockquote with attribution', function () {
    $html = Blade::render(
        '<x-pull-quote attribution="Julienne, mama">"Vrijheid om buiten te zijn."</x-pull-quote>'
    );

    expect($html)
        ->toContain('pull-quote')
        ->toContain('<blockquote')
        ->toContain('<figcaption')
        ->toContain('Julienne, mama');
});

it('pull-quote card variant adds the card modifier class', function () {
    $html = Blade::render(
        '<x-pull-quote variant="card" attribution="Camille, mama">Quote.</x-pull-quote>'
    );

    expect($html)->toContain('pull-quote--card');
});

it('numbered-item renders number chip, title and body slot', function () {
    $html = Blade::render(
        '<x-numbered-item number="1" title="Veilige infrastructuur">Body text.</x-numbered-item>'
    );

    expect($html)
        ->toContain('numbered-item')
        ->toContain('numbered-item__num')
        ->toContain('1')
        ->toContain('Veilige infrastructuur')
        ->toContain('Body text.');
});
