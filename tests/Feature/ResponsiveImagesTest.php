<?php

use App\Models\Activity;
use App\Models\Group;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('media');
});

it('generates a responsive-image srcset for an uploaded chapter cover', function () {
    $group = Group::factory()->create();

    $group->addMedia(UploadedFile::fake()->image('cover.jpg', 1600, 1000))
        ->toMediaCollection('main');

    // withResponsiveImages() + sync queue means the variants exist immediately.
    expect($group->getFirstMedia('main')->getSrcset())
        ->toBeString()
        ->not->toBeEmpty()
        ->toContain('w,'); // a width-descriptor candidate list
});

it('generates a responsive-image srcset for an uploaded activity main image', function () {
    $activity = Activity::factory()->create();

    $activity->addMedia(UploadedFile::fake()->image('ride.jpg', 1600, 1000))
        ->toMediaCollection('main');

    expect($activity->getFirstMedia('main')->getSrcset())->not->toBeEmpty();
});
