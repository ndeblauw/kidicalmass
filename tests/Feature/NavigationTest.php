<?php

use function Pest\Laravel\get;

it('shows the five-item dutch main nav on a public page', function () {
    get('/nl')
        ->assertSee('Kalender')
        ->assertSee('Lokale groepen')
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

it('shows the Steun support CTA in the header, linking to the support page', function () {
    get('/nl')
        ->assertSee('Steun ons')
        ->assertSee(route('membership'), escape: false) // route name kept; path is /steun-ons
        ->assertDontSee('Word lid');
});

it('301s the old /membership path to the new /steun-ons page', function () {
    get('/nl/membership')->assertRedirect(route('membership', ['locale' => 'nl']));
});
