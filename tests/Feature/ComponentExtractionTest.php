<?php

use Illuminate\Support\Facades\Blade;

it('section-heading emits an h2 by default and honours the as prop', function () {
    expect(Blade::render('<x-section-heading>Wie wat doet</x-section-heading>'))
        ->toContain('<h2')
        ->toContain('Wie wat doet')
        ->toContain('</h2>');

    expect(Blade::render('<x-section-heading as="h3">Subkop</x-section-heading>'))
        ->toContain('<h3')
        ->toContain('</h3>');
});

it('pull-quote renders a semantic blockquote with attribution and a card variant', function () {
    $default = Blade::render(
        '<x-pull-quote attribution="Julienne, mama">"Vrijheid om buiten te zijn."</x-pull-quote>'
    );

    expect($default)
        ->toContain('<blockquote')
        ->toContain('<figcaption')
        ->toContain('Julienne, mama')
        ->toContain('Vrijheid om buiten te zijn');

    expect(Blade::render('<x-pull-quote variant="card" attribution="Camille, mama">Quote.</x-pull-quote>'))
        ->toContain('pull-quote--card');
});

it('renders its identifying hook and projects its slot content', function (string $template, array $expected) {
    $html = Blade::render($template);

    foreach ($expected as $needle) {
        expect($html)->toContain($needle);
    }
})->with([
    'intro-text slot' => [
        '<x-intro-text>Paragraph here.</x-intro-text>',
        ['class="intro-text"', 'Paragraph here.'],
    ],
    'intro-text lead variant' => [
        '<x-intro-text size="lead">Big lead.</x-intro-text>',
        ['intro-text--lead', 'Big lead.'],
    ],
    'numbered-item' => [
        '<x-numbered-item number="1" title="Veilige infrastructuur">Body text.</x-numbered-item>',
        ['numbered-item__num', '1', 'Veilige infrastructuur', 'Body text.'],
    ],
    'person-card' => [
        '<x-person-card name="Leticia" role="Coördinatie" />',
        ['person-card', 'Leticia', 'Coördinatie'],
    ],
    'info-card' => [
        '<x-info-card label="Perscontact">bike@kidicalmass.be</x-info-card>',
        ['info-card__label', 'Perscontact', 'bike@kidicalmass.be'],
    ],
    'titled-list-block' => [
        '<x-titled-list-block title="Wat je krijgt"><li>Materiaal en steun</li><li>Opleiding</li></x-titled-list-block>',
        ['titled-list-block__title', 'Wat je krijgt', '<li>Materiaal en steun</li>', '<li>Opleiding</li>'],
    ],
]);
