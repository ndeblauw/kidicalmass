<?php

use Illuminate\Support\Facades\Blade;

test('icon chip renders the chip square with mapped colour, size and tilt', function () {
    $html = Blade::render('<x-icon-chip color="blue" size="md"><svg></svg></x-icon-chip>');

    expect($html)
        ->toContain('bg-kidical-blue')
        ->toContain('size-[2.75rem]')
        ->toContain('rounded-chip')
        ->toContain('-rotate-3')
        ->toContain('<svg></svg>');
});

test('icon chip adds the float shadow only when requested', function () {
    expect(Blade::render('<x-icon-chip :shadow="true">x</x-icon-chip>'))->toContain('shadow-float');
    expect(Blade::render('<x-icon-chip>x</x-icon-chip>'))->not->toContain('shadow-float');
});

test('feature card still renders an icon chip after refactor', function () {
    $html = Blade::render('<x-feature-card icon="clock" title="Test" color="orange">Body</x-feature-card>');

    expect($html)
        ->toContain('bg-kidical-orange')
        ->toContain('size-[4.25rem]')
        ->toContain('rounded-chip')
        ->toContain('Test');
});
