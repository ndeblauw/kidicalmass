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

it('person-card renders name and role', function () {
    $html = Blade::render('<x-person-card name="Leticia" role="Coördinatie" />');

    expect($html)
        ->toContain('person-card')
        ->toContain('Leticia')
        ->toContain('Coördinatie');
});

it('agenda-item renders badge, datetime, title and cta link', function () {
    $html = Blade::render(<<<'BLADE'
        <x-agenda-item
            badge="Rit"
            badge-variant="ride"
            datetime="2026-06-14T14:00"
            when="za 14 jun · 14:00"
            title="Kidical Mass Gent"
            cta-href="/activities/1"
            cta-label="Meer info"
        />
    BLADE);

    expect($html)
        ->toContain('agenda-item')
        ->toContain('agenda-item__badge--ride')
        ->toContain('Rit')
        ->toContain('2026-06-14T14:00')
        ->toContain('za 14 jun · 14:00')
        ->toContain('Kidical Mass Gent')
        ->toContain('href="/activities/1"')
        ->toContain('Meer info');
});

it('agenda-item renders optional location', function () {
    $html = Blade::render(<<<'BLADE'
        <x-agenda-item
            badge="Vergadering"
            badge-variant="meeting"
            datetime="2026-06-14T19:00"
            when="za 14 jun · 19:00"
            title="Teamvergadering"
            location="Café De Fiets"
            cta-href="/activities/2"
            cta-label="Meer info"
        />
    BLADE);

    expect($html)
        ->toContain('agenda-item__loc')
        ->toContain('Café De Fiets');
});

it('info-card renders label and slot content', function () {
    $html = Blade::render(
        '<x-info-card label="Perscontact">bike@kidicalmass.be</x-info-card>'
    );

    expect($html)
        ->toContain('info-card')
        ->toContain('info-card__label')
        ->toContain('Perscontact')
        ->toContain('bike@kidicalmass.be');
});
