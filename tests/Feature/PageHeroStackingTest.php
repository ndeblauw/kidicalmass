<?php

use function Pest\Laravel\get;

/**
 * The interior-page hero (.page-hero) is position:fixed z-index:0, so as a
 * positioned element it paints over any later *static* normal-flow sibling.
 * Every block that scrolls past the hero must therefore be lifted above it
 * (relative z-10), matching .page-panel.
 *
 * On the events page the "binnenkort" opt-in band is the only band rendered
 * outside the panel (between the calendar component and the closing CTA), so
 * it has to carry the lift itself or the fixed hero overlaps it on scroll.
 */
it('lifts the events opt-in band above the fixed page hero', function () {
    get('/nl/events')
        ->assertOk()
        ->assertSee('class="kal-optin relative z-10"', escape: false);
});
