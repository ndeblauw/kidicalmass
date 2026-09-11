<?php

use Illuminate\Support\Facades\Blade;

it('renders the blue theme and logo by default (no color)', function () {
    $html = Blade::render('<x-emails.notification subject="Test">Body</x-emails.notification>');

    expect($html)
        ->toContain('data-theme="blue"')
        ->toContain('img/logos/logo-color.png')
        ->toContain('Test')
        ->toContain('Body');
});

it('renders the blue theme when color is explicitly blue', function () {
    $html = Blade::render('<x-emails.notification color="blue" subject="Test">Body</x-emails.notification>');

    expect($html)
        ->toContain('data-theme="blue"')
        ->toContain('img/logos/logo-color.png');
});

it('renders the yellow theme when color is yellow', function () {
    $html = Blade::render('<x-emails.notification color="yellow" subject="Test">Body</x-emails.notification>');

    expect($html)
        ->toContain('data-theme="yellow"')
        ->toContain('img/logos/logo-color.png')
        ->not->toContain('data-theme="blue"');
});

it('renders the pink theme when color is pink', function () {
    $html = Blade::render('<x-emails.notification color="pink" subject="Test">Body</x-emails.notification>');

    expect($html)
        ->toContain('data-theme="pink"')
        ->toContain('img/logos/logo-color.png')
        ->not->toContain('data-theme="blue"');
});

it('renders the preheader as a hidden element when provided', function () {
    $html = Blade::render('<x-emails.notification preheader="This is the preheader" subject="Test">Body</x-emails.notification>');

    expect($html)
        ->toContain('This is the preheader')
        ->toContain('display:none');
});

it('renders the CTA button and fallback footer link when ctaUrl and ctaLabel are set', function () {
    $html = Blade::render(
        '<x-emails.notification cta-url="https://example.com/activate" cta-label="Activeer" subject="Test">Body</x-emails.notification>',
    );

    expect($html)
        ->toContain('https://example.com/activate')
        ->toContain('Activeer')
        ->toContain('data-cta-button')
        ->toContain('Werkt de knop niet');
});

it('does not render a CTA or footer when ctaUrl is missing', function () {
    $html = Blade::render('<x-emails.notification subject="Test">Body only</x-emails.notification>');

    expect($html)
        ->toContain('Body only')
        ->not->toContain('Werkt de knop niet')
        ->not->toContain('data-cta-button');
});
