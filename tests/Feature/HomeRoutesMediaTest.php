<?php

use function Pest\Laravel\followingRedirects;

it('renders the three route photos and three illustrations on the home page', function () {
    $response = followingRedirects()->get('/');

    $response->assertOk();

    $photos = [
        'img/photography/kids-thumbsup-at-ride.jpg',
        'img/photography/ride-cinquantenaire-crowd.jpg',
        'img/photography/volunteers/volunteers-pink-vests-with-flag.jpg',
    ];

    foreach ($photos as $photo) {
        $response->assertSee($photo, false);
    }

    $illustrations = [
        'img/illustrations/waving-rider.svg',
        'img/illustrations/longtail-with-kid.svg',
        'img/illustrations/volunteer-with-wrench.svg',
    ];

    foreach ($illustrations as $illustration) {
        $response->assertSee($illustration, false);
    }
});
