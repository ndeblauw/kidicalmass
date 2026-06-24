<?php

use App\Enums\RideLifecycleState;
use App\Models\Activity;

it('reports upcoming for a future ride', function () {
    $ride = Activity::factory()->create(['begin_date' => now()->addWeek()]);

    expect($ride->lifecycleState())->toBe(RideLifecycleState::Upcoming)
        ->and($ride->isUpcoming())->toBeTrue()
        ->and($ride->isAwaitingPhotos())->toBeFalse()
        ->and($ride->isRecap())->toBeFalse();
});

it('reports awaiting-photos for a past ride with no gallery', function () {
    $ride = Activity::factory()->past()->create();

    expect($ride->lifecycleState())->toBe(RideLifecycleState::AwaitingPhotos)
        ->and($ride->isAwaitingPhotos())->toBeTrue()
        ->and($ride->isRecap())->toBeFalse();
});

it('reports recap for a past ride that has gallery photos', function () {
    $ride = Activity::factory()->past()->withGallery(3)->create();

    expect($ride->lifecycleState())->toBe(RideLifecycleState::Recap)
        ->and($ride->isRecap())->toBeTrue()
        ->and($ride->isAwaitingPhotos())->toBeFalse();
});

it('treats a ride with no duration as ended once begin_date passes', function () {
    $ride = Activity::factory()->create([
        'begin_date' => now()->subDay(),
        'duration_minutes' => null,
    ]);

    expect($ride->hasEnded())->toBeTrue();
});
