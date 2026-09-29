<?php

use App\Models\PostalCode;
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

it('labels a saved location by its municipality in the current locale', function () {
    PostalCode::create(['zip' => '4000', 'name' => 'Liège', 'name_nl' => 'Luik', 'name_fr' => 'Liège', 'latitude' => 50.6337, 'longitude' => 5.5675]);

    // A cookie saved before the dataset knew municipality names.
    request()->cookies->set('kcm_location', json_encode([
        'zip' => '4000', 'lat' => 50.6758, 'lng' => 5.5462, 'name' => 'Rocourt',
    ]));

    expect(CurrentLocation::resolve())->toBe([
        'zip' => '4000', 'lat' => 50.6758, 'lng' => 5.5462, 'name' => 'Luik',
    ]);

    app()->setLocale('fr');

    expect(CurrentLocation::resolve()['name'])->toBe('Liège');
});
