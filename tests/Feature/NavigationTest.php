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

it('links "Over ons" straight to the /about hub instead of a JS dropdown', function () {
    // The public layout loads no Flux/Alpine JS, so a dropdown menu was inert. The
    // hub page IS the sub-page menu; the nav must link to it directly (desktop +
    // mobile both point at route('about')).
    get('/nl')
        ->assertSee(route('about'), escape: false)
        ->assertDontSee('data-flux-dropdown', escape: false);
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

it('groups the primary nav links in their own white band', function () {
    get('/nl')->assertSee('site-nav__links', escape: false);
});

it('gives the homepage logo the scroll-shrink behaviour but not other pages', function () {
    // The oversized intro logo (twice the size, shrinking on scroll) is homepage-only;
    // the Alpine binding that drives it must not render on inner pages.
    get('/nl')->assertSee('site-logo-anchor--intro', escape: false);
    get('/nl/events')->assertDontSee('site-logo-anchor--intro', escape: false);
});

it('only runs the staggered header reveal intro on the homepage', function () {
    // The header is hidden then revealed (logo, then menu) ahead of the blue-band
    // lead; that intro flag must be homepage-only so inner pages stay visible at once.
    get('/nl')->assertSee('site-header--intro', escape: false);
    get('/nl/events')->assertDontSee('site-header--intro', escape: false);
});
