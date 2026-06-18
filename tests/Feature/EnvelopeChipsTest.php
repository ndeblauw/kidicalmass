<?php

use Illuminate\Support\Facades\Blade;

it('renders three envelope chips, one per brand colour', function () {
    $html = Blade::render('<x-envelope-chips />');

    expect($html)
        ->toContain('envelope-chips__chip--green')
        ->toContain('envelope-chips__chip--red')
        ->toContain('envelope-chips__chip--blue');

    expect(substr_count($html, 'envelope-chips__chip envelope-chips__chip--'))->toBe(3);
});

it('marks the decorative chips aria-hidden', function () {
    expect(Blade::render('<x-envelope-chips />'))->toContain('aria-hidden="true"');
});
