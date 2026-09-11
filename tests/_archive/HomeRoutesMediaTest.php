<?php

use function Pest\Laravel\followingRedirects;

it('renders the three route collages and three illustrations on the home page', function () {
    $response = followingRedirects()->get('/');

    $response->assertOk();

    // Two photos per beat (PAT-20 collage), one collage per route section.
    $photos = [
        'img/photography/ride-child-thumbsup-red-helmet.webp',
        'img/photography/ride-brussels-two-boys-at-start.webp',
        'img/photography/ride-cinquantenaire-crowd.webp',
        'img/photography/cargo-bike-mother-two-kids-flag.webp',
        'img/photography/volunteers-pink-vest-group-cobbles.webp',
        'img/photography/volunteer-fistbump-kids-park.webp',
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
