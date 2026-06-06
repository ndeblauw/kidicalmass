<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;
use App\Support\Location\NextRideFinder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    PostalCode::insert([
        ['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

it('reports no upcoming rides when the calendar is empty', function () {
    $result = NextRideFinder::find(null);

    expect($result['has_upcoming'])->toBeFalse()
        ->and($result['ride'])->toBeNull();
});

it('returns no ride (picker state) when upcoming rides exist but no location is set', function () {
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'postal_code' => '1090',
        'begin_date' => now()->addDays(3),
    ]);

    $result = NextRideFinder::find(null);

    expect($result['has_upcoming'])->toBeTrue()
        ->and($result['ride'])->toBeNull();
});

it('picks the soonest ride within the radius and marks it not far', function () {
    $near = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Jette',
        'postal_code' => '1090',
        'begin_date' => now()->addDays(5),
    ]);
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Gent',
        'postal_code' => '9000',
        'begin_date' => now()->addDays(2),
    ]);

    $result = NextRideFinder::find(['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette']);

    expect($result['ride']->is($near))->toBeTrue()
        ->and($result['is_far'])->toBeFalse()
        ->and($result['distance_km'])->toBe(0.0);
});

it('falls back to the soonest ride anywhere and marks it far when nothing is in range', function () {
    $far = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Gent',
        'postal_code' => '9000',
        'begin_date' => now()->addDays(4),
    ]);

    $result = NextRideFinder::find(['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette']);

    expect($result['ride']->is($far))->toBeTrue()
        ->and($result['is_far'])->toBeTrue()
        ->and($result['distance_km'])->toBeGreaterThan(7.0);
});

it('treats a ride with an unresolvable postal code as far with an unknown distance', function () {
    $ride = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Onbekend',
        'postal_code' => null,
        'begin_date' => now()->addDays(3),
    ]);

    $result = NextRideFinder::find(['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette']);

    expect($result['ride']->is($ride))->toBeTrue()
        ->and($result['is_far'])->toBeTrue()
        ->and($result['distance_km'])->toBeNull()
        ->and($result['has_upcoming'])->toBeTrue();
});
