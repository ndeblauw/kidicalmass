<?php

use function Pest\Laravel\get;

it('shows the five-item dutch main nav on a public page', function () {
    get('/nl')
        ->assertSee('Kalender')
        ->assertSee('Afdelingen')
        ->assertSee('Voor het eerst')
        ->assertSee('Meehelpen')
        ->assertSee('Over ons')
        ->assertDontSee('Register');
});

it('links the footer to contact, membership and the combined legal page', function () {
    // Privacy + cookies are one page now; the footer carries a single legal link.
    get('/nl')
        ->assertSee(route('contact'))
        ->assertSee(route('membership'))
        ->assertSee(route('privacy'))
        ->assertSee('Privacy & cookies');
});

it('301s the old /cookies path to the combined privacy page', function () {
    get('/nl/cookies')->assertRedirect(route('privacy', ['locale' => 'nl']));
});
