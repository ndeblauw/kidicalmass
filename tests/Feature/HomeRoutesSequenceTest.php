<?php

use function Pest\Laravel\get;

it('renders the three home route sections with titles, copy and CTAs in order', function () {
    $response = get(route('home'));

    $response->assertOk();

    // Titles, in the agreed funnel order.
    $body = $response->getContent();
    $newPos = strpos($body, 'Nieuw hier?');
    $findPos = strpos($body, 'Vind je lokale groep');
    $helpPos = strpos($body, 'Help mee');

    expect($newPos)->not->toBeFalse();
    expect($findPos)->not->toBeFalse();
    expect($helpPos)->not->toBeFalse();
    expect($newPos)->toBeLessThan($findPos);
    expect($findPos)->toBeLessThan($helpPos);

    // Each section routes out to its page.
    $response->assertSee(route('getting-started'), false);
    $response->assertSee(route('groups.index'), false);
    $response->assertSee(route('volunteer'), false);
});

it('points the home closing-CTA at membership, not the groups index', function () {
    $response = get(route('home'));

    $response->assertOk();
    $response->assertSee(route('membership'), false);
    $response->assertSee('Steun ons');
});
