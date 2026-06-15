<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Support\Location\NextRideFinder;

test('finder returns a grouped preview of upcoming rides', function () {
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDays(3),
    ]);
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDays(10),
    ]);

    $result = NextRideFinder::find(null);

    expect($result['has_upcoming'])->toBeTrue()
        ->and($result['ride'])->toBeNull()
        ->and($result['upcoming_preview'])->toBeArray()
        ->and($result['upcoming_preview'])->not->toBeEmpty();

    $firstDay = array_values($result['upcoming_preview'])[0];
    expect($firstDay[0]['item'])->toBeInstanceOf(Activity::class);
});

test('preview is empty when there are no upcoming rides', function () {
    expect(NextRideFinder::find(null)['upcoming_preview'])->toBe([]);
});
