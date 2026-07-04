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

it('pull-quote renders a semantic blockquote with attribution and a marker variant', function () {
    $default = Blade::render(
        '<x-pull-quote attribution="Julienne, mama">"Vrijheid om buiten te zijn."</x-pull-quote>'
    );

    expect($default)
        ->toContain('<blockquote')
        ->toContain('<figcaption')
        ->toContain('Julienne, mama')
        ->toContain('Vrijheid om buiten te zijn');

    // Marker: attribution splits into a name plus middot-joined detail parts,
    // and edge quote marks are stripped (the CSS mark does the quoting).
    $marker = Blade::render(
        '<x-pull-quote variant="marker" attribution="Fatima, mama van drie kinderen, Jette">“Ik ben constant bang.”</x-pull-quote>'
    );

    expect($marker)
        ->toContain('pull-quote--marker')
        ->toContain('pull-quote__name')
        ->toContain('Fatima')
        ->toContain('pull-quote__detail')
        ->toContain('Jette')
        ->toContain('aria-hidden="true"')
        ->toContain('Ik ben constant bang.')
        ->not->toContain('“')
        ->not->toContain('”');

    // A one-part attribution renders as a plain caption, no orphaned middots.
    expect(Blade::render('<x-pull-quote variant="marker" attribution="Julienne">Quote.</x-pull-quote>'))
        ->toContain('Julienne')
        ->not->toContain('pull-quote__sep');
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
