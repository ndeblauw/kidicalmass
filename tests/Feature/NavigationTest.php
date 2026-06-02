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

it('links the footer to contact, membership and legal pages', function () {
    get('/nl')
        ->assertSee(route('contact'))
        ->assertSee(route('membership'))
        ->assertSee(route('privacy'))
        ->assertSee(route('cookies'));
});
