<?php

use function Pest\Laravel\get;

it('wraps the page bottom in a yellow footer zone', function () {
    get(route('home'))
        ->assertOk()
        ->assertSee('site-footer-zone', escape: false)
        ->assertSee('footerbunch-yellow.png', escape: false);
});

it('keeps the partner card inside the footer zone on showcase routes', function () {
    get(route('home'))
        ->assertOk()
        ->assertSee('partner-strip', escape: false);
});

it('still renders the dark footer bottom bar site-wide', function () {
    get(route('activities.index'))
        ->assertOk()
        ->assertSee('Kidical Mass Belgium', escape: false);
});

it('replaces the about-cta card with the new closing CTA on about pages', function () {
    get(route('about.mission'))
        ->assertOk()
        ->assertSee('Samen maken we straten veiliger', escape: false)
        ->assertDontSee('about-cta__content', escape: false);
});
