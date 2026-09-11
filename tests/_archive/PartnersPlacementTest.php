<?php

use function Pest\Laravel\get;

it('renders the partner showcase band on the home page', function () {
    get(route('home'))
        ->assertOk()
        ->assertSee('partner-strip', escape: false);
});

it('renders the partner showcase band on an About narrative page', function () {
    get(route('about.mission'))
        ->assertOk()
        ->assertSee('partner-strip', escape: false);
});

it('does not render the partner showcase band on a non-showcase page', function () {
    get(route('activities.index'))
        ->assertOk()
        ->assertDontSee('partner-strip', escape: false);
});

it('renders the funder credit in the footer on every page', function () {
    get(route('activities.index'))
        ->assertOk()
        ->assertSee('Mede mogelijk gemaakt door', escape: false);
});
