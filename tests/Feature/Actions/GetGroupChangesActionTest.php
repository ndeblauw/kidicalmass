<?php

use App\Actions\GetGroupChangesAction;
use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->group = Group::factory()->create();
    $this->author = User::factory()->create();
    $this->startDate = now()->subMonth();
    $this->endDate = now();
});

it('returns no changes when nothing happened in the date range', function () {
    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->hasAny())->toBeFalse();
    expect($changes->summary())->toEqual([
        'activities_added' => 0,
        'activities_updated' => 0,
        'members_added' => 0,
        'captains_added' => 0,
        'pinkvests_added' => 0,
        'interested_added' => 0,
        'articles_added' => 0,
        'articles_updated' => 0,
    ]);
});

it('detects new activities in the date range', function () {
    $activity = Activity::factory()->create([
        'author_id' => $this->author->id,
        'created_at' => now()->subDays(5),
        'updated_at' => now()->subDays(5),
    ]);
    $this->group->activities()->attach($activity);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->newActivities)->toHaveCount(1)
        ->and($changes->newActivities->first()->is($activity))->toBeTrue()
        ->and($changes->updatedActivities)->toBeEmpty()
        ->and($changes->hasAny())->toBeTrue();
});

it('excludes activities created before the date range', function () {
    $activity = Activity::factory()->create([
        'author_id' => $this->author->id,
        'created_at' => now()->subMonths(3),
    ]);
    $this->group->activities()->attach($activity);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->newActivities)->toBeEmpty();
});

it('detects updated activities in the date range', function () {
    $activity = Activity::factory()->create([
        'author_id' => $this->author->id,
        'created_at' => now()->subMonths(3),
        'updated_at' => now()->subDays(5),
    ]);
    $this->group->activities()->attach($activity);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->updatedActivities)->toHaveCount(1)
        ->and($changes->newActivities)->toBeEmpty();
});

it('excludes activities that were created and updated in range from updated list', function () {
    $activity = Activity::factory()->create([
        'author_id' => $this->author->id,
        'created_at' => now()->subDays(5),
        'updated_at' => now()->subDays(5),
    ]);
    $this->group->activities()->attach($activity);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->newActivities)->toHaveCount(1)
        ->and($changes->updatedActivities)->toBeEmpty();
});

it('does not include activities from other groups', function () {
    $otherGroup = Group::factory()->create();
    $activity = Activity::factory()->create([
        'author_id' => $this->author->id,
        'created_at' => now()->subDays(5),
    ]);
    $otherGroup->activities()->attach($activity);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->newActivities)->toBeEmpty();
});

it('detects new captains', function () {
    $captain = User::factory()->create();
    $this->group->users()->attach($captain, [
        'role' => 'captain',
        'created_at' => now()->subDays(5),
        'updated_at' => now()->subDays(5),
    ]);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->newCaptains)->toHaveCount(1)
        ->and($changes->newPinkVests)->toBeEmpty()
        ->and($changes->newInterested)->toBeEmpty();
});

it('detects new pinkvests', function () {
    $pinkvest = User::factory()->create();
    $this->group->users()->attach($pinkvest, [
        'role' => 'pinkvest',
        'created_at' => now()->subDays(5),
        'updated_at' => now()->subDays(5),
    ]);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->newPinkVests)->toHaveCount(1)
        ->and($changes->newCaptains)->toBeEmpty()
        ->and($changes->newInterested)->toBeEmpty();
});

it('detects new interested members', function () {
    $interested = User::factory()->create();
    $this->group->users()->attach($interested, [
        'role' => null,
        'created_at' => now()->subDays(5),
        'updated_at' => now()->subDays(5),
    ]);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->newInterested)->toHaveCount(1)
        ->and($changes->newCaptains)->toBeEmpty()
        ->and($changes->newPinkVests)->toBeEmpty();
});

it('detects new articles in the date range', function () {
    $article = Article::factory()->create([
        'author_id' => $this->author->id,
        'created_at' => now()->subDays(5),
        'updated_at' => now()->subDays(5),
    ]);
    $this->group->articles()->attach($article);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->newArticles)->toHaveCount(1)
        ->and($changes->newArticles->first()->is($article))->toBeTrue()
        ->and($changes->updatedArticles)->toBeEmpty();
});

it('detects updated articles in the date range', function () {
    $article = Article::factory()->create([
        'author_id' => $this->author->id,
        'created_at' => now()->subMonths(3),
        'updated_at' => now()->subDays(5),
    ]);
    $this->group->articles()->attach($article);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->updatedArticles)->toHaveCount(1)
        ->and($changes->newArticles)->toBeEmpty();
});

it('collects recent rides that happened in range and have gallery photos', function () {
    Storage::fake('media');

    $withPhotos = Activity::factory()->create([
        'author_id' => $this->author->id,
        'begin_date' => now()->subDays(5),
    ]);
    $withPhotos->addMedia(UploadedFile::fake()->image('rit.jpg', 40, 30))->toMediaCollection('gallery');
    $this->group->activities()->attach($withPhotos);

    $withoutPhotos = Activity::factory()->create([
        'author_id' => $this->author->id,
        'begin_date' => now()->subDays(6),
    ]);
    $this->group->activities()->attach($withoutPhotos);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->recentRidesWithPhotos)->toHaveCount(1)
        ->and($changes->recentRidesWithPhotos->first()->is($withPhotos))->toBeTrue()
        ->and($changes->hasAny())->toBeTrue();
});

it('excludes rides outside the window or without photos from recentRidesWithPhotos', function () {
    Storage::fake('media');

    $old = Activity::factory()->create([
        'author_id' => $this->author->id,
        'begin_date' => now()->subMonths(3),
    ]);
    $old->addMedia(UploadedFile::fake()->image('oud.jpg', 40, 30))->toMediaCollection('gallery');
    $this->group->activities()->attach($old);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->recentRidesWithPhotos)->toBeEmpty();
});

it('collects published upcoming activities within the look-ahead horizon', function () {
    $soon = Activity::factory()->create([
        'author_id' => $this->author->id,
        'begin_date' => now()->addWeeks(2),
        'published' => true,
    ]);
    $this->group->activities()->attach($soon);

    $unpublished = Activity::factory()->create([
        'author_id' => $this->author->id,
        'begin_date' => now()->addWeeks(3),
        'published' => false,
    ]);
    $this->group->activities()->attach($unpublished);

    $tooFar = Activity::factory()->create([
        'author_id' => $this->author->id,
        'begin_date' => now()->addMonths(6),
        'published' => true,
    ]);
    $this->group->activities()->attach($tooFar);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->upcomingActivities)->toHaveCount(1)
        ->and($changes->upcomingActivities->first()->is($soon))->toBeTrue();
});

it('does not let upcoming activities alone trigger hasAny', function () {
    // Planned at year-start (created/updated before the window) so it is only
    // "upcoming", not a fresh new/updated activity.
    $soon = Activity::factory()->create([
        'author_id' => $this->author->id,
        'begin_date' => now()->addWeeks(2),
        'published' => true,
        'created_at' => now()->subMonths(3),
        'updated_at' => now()->subMonths(3),
    ]);
    $this->group->activities()->attach($soon);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->upcomingActivities)->toHaveCount(1)
        ->and($changes->hasAny())->toBeFalse();
});

it('uses default dates when none are provided', function () {
    $activity = Activity::factory()->create([
        'author_id' => $this->author->id,
        'created_at' => now()->subDays(2),
    ]);
    $this->group->activities()->attach($activity);

    $changes = (new GetGroupChangesAction($this->group))->execute();

    expect($changes->newActivities)->toHaveCount(1);
});

it('accepts a custom date range', function () {
    $oldActivity = Activity::factory()->create([
        'author_id' => $this->author->id,
        'created_at' => now()->subDays(60),
    ]);
    $this->group->activities()->attach($oldActivity);

    $customStart = now()->subDays(90);
    $customEnd = now()->subDays(30);

    $changes = (new GetGroupChangesAction($this->group, $customStart, $customEnd))->execute();

    expect($changes->newActivities)->toHaveCount(1);
});

it('provides a correct summary with mixed changes', function () {
    $activity = Activity::factory()->create([
        'author_id' => $this->author->id,
        'created_at' => now()->subDays(5),
        'updated_at' => now()->subDays(5),
    ]);
    $this->group->activities()->attach($activity);

    $captain = User::factory()->create();
    $this->group->users()->attach($captain, [
        'role' => 'captain',
        'created_at' => now()->subDays(3),
        'updated_at' => now()->subDays(3),
    ]);

    $article = Article::factory()->create([
        'author_id' => $this->author->id,
        'created_at' => now()->subDays(7),
        'updated_at' => now()->subDays(7),
    ]);
    $this->group->articles()->attach($article);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->summary())->toEqual([
        'activities_added' => 1,
        'activities_updated' => 0,
        'members_added' => 1,
        'captains_added' => 1,
        'pinkvests_added' => 0,
        'interested_added' => 0,
        'articles_added' => 1,
        'articles_updated' => 0,
    ]);
});

it('correctly reports members_added_count', function () {
    $this->group->users()->attach(User::factory()->create(), ['role' => 'captain', 'created_at' => now()->subDays(5), 'updated_at' => now()->subDays(5)]);
    $this->group->users()->attach(User::factory()->create(), ['role' => 'pinkvest', 'created_at' => now()->subDays(4), 'updated_at' => now()->subDays(4)]);
    $this->group->users()->attach(User::factory()->create(), ['role' => null, 'created_at' => now()->subDays(3), 'updated_at' => now()->subDays(3)]);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->membersAddedCount())->toBe(3);
});

it('ignores members who joined outside the date range', function () {
    $user = User::factory()->create();
    $this->group->users()->attach($user, [
        'role' => 'captain',
        'created_at' => now()->subMonths(6),
        'updated_at' => now()->subMonths(6),
    ]);

    $changes = (new GetGroupChangesAction($this->group, $this->startDate, $this->endDate))->execute();

    expect($changes->newCaptains)->toBeEmpty();
    expect($changes->membersAddedCount())->toBe(0);
});

it('can be called via the group model convenience method', function () {
    $activity = Activity::factory()->create([
        'author_id' => $this->author->id,
        'created_at' => now()->subDays(5),
        'updated_at' => now()->subDays(5),
    ]);
    $this->group->activities()->attach($activity);

    $changes = $this->group->changes($this->startDate, $this->endDate);

    expect($changes->newActivities)->toHaveCount(1);
});

it('uses default dates when called via the convenience method without parameters', function () {
    $activity = Activity::factory()->create([
        'author_id' => $this->author->id,
        'created_at' => now()->subDays(2),
        'updated_at' => now()->subDays(2),
    ]);
    $this->group->activities()->attach($activity);

    $changes = $this->group->changes();

    expect($changes->newActivities)->toHaveCount(1);
});
