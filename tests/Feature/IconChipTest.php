<?php

use Illuminate\Support\Facades\Blade;

test('icon chip renders its slotted icon and reflects the colour and size variants', function () {
    $html = Blade::render('<x-icon-chip color="blue" size="md"><svg></svg></x-icon-chip>');

    expect($html)
        ->toContain('<svg></svg>')        // the icon slot renders
        ->toContain('data-color="blue"')  // colour variant wired through
        ->toContain('data-size="md"');    // size variant wired through
});

test('icon chip flags the float shadow only when requested', function () {
    expect(Blade::render('<x-icon-chip :shadow="true">x</x-icon-chip>'))->toContain('data-shadow');
    expect(Blade::render('<x-icon-chip>x</x-icon-chip>'))->not->toContain('data-shadow');
});

test('feature card embeds an icon chip carrying its colour', function () {
    $html = Blade::render('<x-feature-card icon="clock" title="Test" color="orange">Body</x-feature-card>');

    expect($html)
        ->toContain('data-icon-chip')       // the chip is present after the refactor
        ->toContain('data-color="orange"')  // feature card forwards its colour to the chip
        ->toContain('Test');                // title renders
});
