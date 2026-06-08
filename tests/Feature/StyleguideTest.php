<?php

use function Pest\Laravel\get;

it('serves the styleguide in non-production', function () {
    get(route('styleguide'))
        ->assertOk()
        ->assertSee('id="tokens"', false)
        ->assertSee('id="componenten"', false)
        ->assertSee('id="nog-te-extraheren"', false);
});

it('renders the live component demos', function () {
    get(route('styleguide'))
        ->assertOk()
        ->assertSee('cta-button')
        ->assertSee('feature-card')
        ->assertSee('ride-row')
        ->assertSee('stat-card');
});
