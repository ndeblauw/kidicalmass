<?php

use function Pest\Laravel\get;

it('renders the "Hoe je kan helpen" roles as a carousel', function () {
    $response = get('/nl/help-out');

    $response->assertOk();

    // Carousel scaffolding (same approach as the chapter teamband).
    $response->assertSee('ho-roles__track', escape: false);
    $response->assertSee('ho-roles__card', escape: false);
    $response->assertSee('ho-roles__fg', escape: false);    // fixed illustration + title anchor
    $response->assertSee('x-ref="fg"', escape: false);      // anchor the per-card opacity fade reads against
    $response->assertSee('x-ref="track"', escape: false);   // Alpine paging + end-button hook

    // The section no longer uses the static two-column promises layout.
    $response->assertDontSee('activity-promises__layout', escape: false);
});

it('shows all five help-type cards with their nav controls', function () {
    $response = get('/nl/help-out');

    foreach (['Roze hesje', 'Mede-organisator', 'Communicator', 'Fotograaf', 'DJ'] as $role) {
        $response->assertSee($role);
    }

    $response->assertSee('Vorige rollen', escape: false);
    $response->assertSee('Volgende rollen', escape: false);
});
