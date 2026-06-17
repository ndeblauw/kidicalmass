<?php

use App\Models\Group;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('media');
});

afterEach(function () {
    // The production-guard test flips the app environment; reset it so later
    // tests in the same process never inherit a stale 'production' value.
    app()->detectEnvironment(fn () => 'testing');
});

it('attaches the requested number of gallery photos to a group', function () {
    $group = Group::factory()->create();

    $this->artisan('dev:seed-group-gallery', ['--group' => [$group->id], '--count' => 3])
        ->assertSuccessful();

    expect($group->fresh()->getMedia('gallery'))->toHaveCount(3);
});

it('is idempotent so re-running does not duplicate photos', function () {
    $group = Group::factory()->create();

    $this->artisan('dev:seed-group-gallery', ['--group' => [$group->id], '--count' => 3])->assertSuccessful();
    $this->artisan('dev:seed-group-gallery', ['--group' => [$group->id], '--count' => 3])->assertSuccessful();

    expect($group->fresh()->getMedia('gallery'))->toHaveCount(3);
});

it('refuses to run in production', function () {
    app()->detectEnvironment(fn () => 'production');
    $group = Group::factory()->create();

    $this->artisan('dev:seed-group-gallery', ['--group' => [$group->id]])->assertFailed();

    expect($group->fresh()->getMedia('gallery'))->toHaveCount(0);
});
