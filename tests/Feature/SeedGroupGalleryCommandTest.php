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

it('attaches explicit --source images in order, cover first', function () {
    $group = Group::factory()->create();

    $cover = public_path('img/photography/ride-cinquantenaire-crowd.jpg');
    $second = public_path('img/photography/kids-thumbsup-at-ride.jpg');

    $this->artisan('dev:seed-group-gallery', [
        '--group' => [$group->id],
        '--count' => 2,
        '--source' => [$cover, $second],
    ])->assertSuccessful();

    $gallery = $group->fresh()->getMedia('gallery');

    expect($gallery)->toHaveCount(2)
        ->and($gallery->first()->name)->toBe('ride-cinquantenaire-crowd')
        ->and($gallery->get(1)->name)->toBe('kids-thumbsup-at-ride');
});

it('skips --source paths that do not exist', function () {
    $group = Group::factory()->create();

    $real = public_path('img/photography/ride-cinquantenaire-crowd.jpg');

    $this->artisan('dev:seed-group-gallery', [
        '--group' => [$group->id],
        '--count' => 2,
        '--source' => [$real, 'does/not/exist.jpg'],
    ])->assertSuccessful();

    // count cycles over the one resolvable source.
    expect($group->fresh()->getMedia('gallery'))->toHaveCount(2);
});

it('refuses to run in production', function () {
    app()->detectEnvironment(fn () => 'production');
    $group = Group::factory()->create();

    $this->artisan('dev:seed-group-gallery', ['--group' => [$group->id]])->assertFailed();

    expect($group->fresh()->getMedia('gallery'))->toHaveCount(0);
});
