<?php

use App\Support\Location\Proximity;
use Illuminate\Support\Collection;

it('computes haversine distance between two points', function () {
    // Jette (50.8782, 4.3265) -> Gent (51.0543, 3.7174) ~ 45 km
    $km = Proximity::distanceKm(['lat' => 50.8782, 'lng' => 4.3265], ['lat' => 51.0543, 'lng' => 3.7174]);

    expect($km)->toBeGreaterThan(42)->toBeLessThan(48);
});

it('partitions items into nearby and far, preserving input order', function () {
    $origin = ['lat' => 50.8782, 'lng' => 4.3265]; // Jette
    $coords = [
        'jette' => ['lat' => 50.8782, 'lng' => 4.3265],
        'schaarbeek' => ['lat' => 50.8676, 'lng' => 4.3737], // ~3.5 km
        'gent' => ['lat' => 51.0543, 'lng' => 3.7174], // ~45 km
        'unknown' => null, // no coordinates -> far
    ];
    $items = new Collection(['jette', 'schaarbeek', 'gent', 'unknown']);

    $result = Proximity::partitionByRadius($items, $origin, 7, fn ($key) => $coords[$key]);

    expect($result['nearby']->pluck('item')->all())->toBe(['jette', 'schaarbeek']);
    expect($result['far']->pluck('item')->all())->toBe(['gent', 'unknown']);
    expect($result['nearby']->first()['distance_km'])->toBe(0.0);
});

it('keeps an item sitting exactly on the radius (boundary is inclusive)', function () {
    $origin = ['lat' => 50.8782, 'lng' => 4.3265];
    $items = new Collection(['exactly_here']);

    // Distance is exactly 0 km against a 0 km radius: an inclusive boundary (<=)
    // keeps it nearby; an exclusive one (<) would wrongly drop it to far.
    $result = Proximity::partitionByRadius($items, $origin, 0, fn () => $origin);

    expect($result['nearby']->pluck('item')->all())->toBe(['exactly_here'])
        ->and($result['far'])->toBeEmpty();
});

it('ranks the n nearest items by distance, dropping null-coord items', function () {
    $origin = ['lat' => 50.8782, 'lng' => 4.3265]; // Jette
    $coords = [
        'gent' => ['lat' => 51.0543, 'lng' => 3.7174],      // ~45 km
        'schaarbeek' => ['lat' => 50.8676, 'lng' => 4.3737], // ~3.5 km
        'jette' => ['lat' => 50.8782, 'lng' => 4.3265],      // 0 km
        'antwerpen' => ['lat' => 51.2194, 'lng' => 4.4025],  // ~38 km
        'unknown' => null,                                   // excluded
    ];
    $items = new Collection(['gent', 'schaarbeek', 'jette', 'antwerpen', 'unknown']);

    $result = Proximity::nearest($items, $origin, 3, fn ($key) => $coords[$key]);

    // Sorted ascending by distance, capped at 3, null-coord item excluded.
    expect($result->pluck('item')->all())->toBe(['jette', 'schaarbeek', 'antwerpen']);
    expect($result->first()['distance_km'])->toBe(0.0);
});

it('returns fewer than n when fewer ranked items exist', function () {
    $origin = ['lat' => 50.8782, 'lng' => 4.3265];
    $coords = ['jette' => ['lat' => 50.8782, 'lng' => 4.3265], 'unknown' => null];
    $items = new Collection(['jette', 'unknown']);

    $result = Proximity::nearest($items, $origin, 4, fn ($key) => $coords[$key]);

    expect($result->pluck('item')->all())->toBe(['jette']);
});
