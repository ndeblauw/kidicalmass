<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use Database\Seeders\ChapterShowcaseSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('media');
});

it('gives the Schaarbeek chapter a posed group photo as its hero cover', function () {
    Group::factory()->create(['shortname' => 'schaarbeek', 'name' => 'Schaarbeek']);

    (new ChapterShowcaseSeeder)->run();

    $cover = Group::where('shortname', 'schaarbeek')->first()->getMedia('gallery')->first();

    expect($cover)->not->toBeNull()
        ->and($cover->name)->toBe('ride-group-photo-bandstand');
});

it('gives the Schaarbeek chapter a next ride with a real GPX route to draw', function () {
    $group = Group::factory()->create(['shortname' => 'schaarbeek', 'name' => 'Schaarbeek']);

    (new ChapterShowcaseSeeder)->run();

    $nextRide = Activity::query()
        ->whereHas('groups', fn ($query) => $query->where('groups.id', $group->id))
        ->where('activity_type', ActivityType::KIDICALMASS)
        ->where('begin_date', '>=', now())
        ->orderBy('begin_date')
        ->first();

    expect($nextRide)->not->toBeNull()
        ->and($nextRide->getFirstMedia('gpx'))->not->toBeNull()
        ->and($nextRide->route_coordinates)->not->toBeEmpty();
});
