<?php

use App\Support\Location\CurrentLocation;

it('returns null when no location cookie is set', function () {
    expect(CurrentLocation::resolve())->toBeNull();
});

it('reads a valid location cookie into an array', function () {
    request()->cookies->set('kcm_location', json_encode([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]));

    expect(CurrentLocation::resolve())->toBe([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]);
});

it('returns null for a malformed cookie', function () {
    request()->cookies->set('kcm_location', 'not-json');

    expect(CurrentLocation::resolve())->toBeNull();
});
