<?php

use Illuminate\Support\Facades\Blade;

it('renders a closing CTA heading and button on a yellow band', function () {
    $html = Blade::render(
        '<x-closing-cta heading="Klaar voor je eerste rit?" href="/events" label="Vind een rit" />'
    );

    expect($html)
        ->toContain('Klaar voor je eerste rit?')
        ->toContain('Vind een rit')
        ->toContain('bg-kidical-yellow')
        ->toContain('href="/events"');
});
