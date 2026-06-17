<?php

use Illuminate\Support\Facades\Blade;

it('renders a pastel blue background and logo by default (no color)', function () {
    $html = Blade::render('<x-emails.notification subject="Test">Body</x-emails.notification>');

    expect($html)
        ->toContain('#B7E7F0')   // pastel blue outer background
        ->toContain('img/logos/logo-color.png')
        ->toContain('Test')
        ->toContain('Body');
});

it('renders a pastel blue background when color is explicitly blue', function () {
    $html = Blade::render('<x-emails.notification color="blue" subject="Test">Body</x-emails.notification>');

    expect($html)
        ->toContain('#B7E7F0')
        ->toContain('img/logos/logo-color.png');
});

it('renders a pastel yellow background and logo when color is yellow', function () {
    $html = Blade::render('<x-emails.notification color="yellow" subject="Test">Body</x-emails.notification>');

    expect($html)
        ->toContain('#FEF3D5')   // pastel yellow background
        ->toContain('img/logos/logo-color.png')
        ->not->toContain('#B7E7F0');
});

it('renders a pastel pink background and logo when color is pink', function () {
    $html = Blade::render('<x-emails.notification color="pink" subject="Test">Body</x-emails.notification>');

    expect($html)
        ->toContain('#fce4ec')   // pastel pink background
        ->toContain('img/logos/logo-color.png')
        ->not->toContain('#B7E7F0');
});

it('renders the preheader as a hidden element when provided', function () {
    $html = Blade::render('<x-emails.notification preheader="This is the preheader" subject="Test">Body</x-emails.notification>');

    expect($html)
        ->toContain('This is the preheader')
        ->toContain('display:none');
});

it('renders the CTA button with theme color and fallback footer link when ctaUrl and ctaLabel are set', function () {
    $html = Blade::render(
        '<x-emails.notification cta-url="https://example.com/activate" cta-label="Activeer" subject="Test">Body</x-emails.notification>',
    );

    expect($html)
        ->toContain('https://example.com/activate')
        ->toContain('Activeer')
        ->toContain('#1d67cd')   // default blue button
        ->toContain('Werkt de knop niet');
});

it('does not render a CTA or footer when ctaUrl is missing', function () {
    $html = Blade::render('<x-emails.notification subject="Test">Body only</x-emails.notification>');

    expect($html)
        ->not->toContain('Werkt de knop niet')
        ->not->toContain('border-radius:9999px');
});
