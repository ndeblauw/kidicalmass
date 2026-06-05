<?php

use App\Support\Location\Proximity;
use Illuminate\Support\Collection;

it('computes haversine distance between two points', function () {
    // Jette (50.8782, 4.3265) -> Gent (51.0543, 3.7174) ~ 45 km
    $km = Proximity::distanceKm(['lat' => 50.8782, 'lng' => 4.3265], ['lat' => 51.0543, 'lng' => 3.7174]);

    expect($km)->toBeGreaterThan(42)->toBeLessThan(48);
});

it('partitions items into nearby and far, annotated and sorted by distance', function () {
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
