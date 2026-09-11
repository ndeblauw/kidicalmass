<?php

use Illuminate\Support\Facades\Blade;

it('renders variant and size modifier classes', function () {
    $html = Blade::render('<x-cta-button href="/x" variant="secondary" size="sm">Go</x-cta-button>');

    expect($html)
        ->toContain('cta-button--secondary')
        ->toContain('cta-button--sm')
        ->toContain('href="/x"');
});

it('renders the ghost variant', function () {
    $html = Blade::render('<x-cta-button href="/x" variant="ghost">Go</x-cta-button>');

    expect($html)->toContain('cta-button--ghost');
});

it('renders a full-width block button', function () {
    $html = Blade::render('<x-cta-button href="/x" :block="true">Go</x-cta-button>');

    expect($html)->toContain('cta-button--block');
});

it('marks disabled buttons inert and drops the href', function () {
    $html = Blade::render('<x-cta-button href="/x" :disabled="true">Go</x-cta-button>');

    expect($html)
        ->toContain('cta-button--disabled')
        ->toContain('aria-disabled="true"')
        ->not->toContain('href="/x"');
});

it('shows a spinner and marks aria-busy when loading', function () {
    $html = Blade::render('<x-cta-button href="/x" :loading="true">Go</x-cta-button>');

    expect($html)
        ->toContain('cta-button--loading')
        ->toContain('cta-button__spinner')
        ->toContain('aria-busy="true"')
        ->not->toContain('href="/x"');
});
