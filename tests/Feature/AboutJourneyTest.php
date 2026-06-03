<?php

use function Pest\Laravel\get;

// The "journey package" (2026-06-03): the About corridor must hand deciders the
// right exit, and ask for Steun where the case is hottest. See about-journey.md.

it('triages the hub by intention, routing to the exits deciders came for', function () {
    get('/nl/about')
        ->assertOk()
        ->assertSee('Waar ben je naar op zoek?')
        ->assertSee('Of lees meer over de beweging')
        // The intention strip routes to actions, not just more reading.
        ->assertSee(route('volunteer'), escape: false)
        ->assertSee(route('about.press'), escape: false)
        ->assertSee(route('about.partners'), escape: false)
        ->assertSee(route('membership'), escape: false)
        ->assertDontSee('—');
});

it('asks for Steun on Mission at the post-stats peak-intent moment', function () {
    get('/nl/about/mission')
        ->assertOk()
        ->assertSee('Al onze ritten zijn gratis')
        ->assertSee(route('membership'), escape: false)
        // Exits match intentions: Help mee is the decider's primary action.
        ->assertSee(route('volunteer'), escape: false)
        ->assertDontSee('—');
});

it('asks for Steun on Organisation right after the no-paid-staff money model', function () {
    get('/nl/about/organisation')
        ->assertOk()
        ->assertSee('Geen hoofdkantoor, geen betaald personeel')
        ->assertSee(route('membership'), escape: false)
        ->assertDontSee('—');
});

it('offers Steun as a closing action on Vision', function () {
    get('/nl/about/vision')
        ->assertOk()
        ->assertSee(route('volunteer'), escape: false)
        ->assertSee(route('membership'), escape: false)
        ->assertDontSee('—');
});
