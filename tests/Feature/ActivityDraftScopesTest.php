<?php

use App\Models\Activity;
use App\Models\Group;
use App\Models\User;

use function Pest\Laravel\get;

it('returns draft activities from the drafts scope', function () {
    $draft = Activity::factory()->create(['is_published' => false]);
    $published = Activity::factory()->create(['is_published' => true]);

    $drafts = Activity::drafts()->get();

    expect($drafts)->toHaveCount(1)
        ->and($drafts->first()->id)->toBe($draft->id);
});

it('returns published activities from the published scope', function () {
    Activity::factory()->create(['is_published' => false]);
    $published = Activity::factory()->create(['is_published' => true]);

    $result = Activity::published()->get();

    expect($result)->toHaveCount(1)
        ->and($result->first()->id)->toBe($published->id);
});

it('returns 404 for draft activities on the public show page', function () {
    $group = Group::factory()->create();
    $activity = Activity::factory()
        ->hasAttached($group)
        ->create(['is_published' => false]);

    get('/nl/events/'.$activity->id)->assertNotFound();
});

it('returns 404 for draft activities on the ical endpoint', function () {
    $group = Group::factory()->create();
    $activity = Activity::factory()
        ->hasAttached($group)
        ->create(['is_published' => false]);

    get('/nl/events/'.$activity->id.'/ical')->assertNotFound();
});

it('returns 200 for published activities on the public show page', function () {
    $group = Group::factory()->create();
    $activity = Activity::factory()
        ->hasAttached($group)
        ->create(['is_published' => true]);

    get('/nl/events/'.$activity->id)->assertOk();
});

it('can create a draft with minimal fields only', function () {
    $author = User::factory()->create();
    $group = Group::factory()->create();

    $draft = Activity::create([
        'title_nl' => 'Minimal Draft',
        'title_fr' => 'Minimal Draft FR',
        'begin_date' => now()->addMonth(),
        'author_id' => $author->id,
    ]);
    $draft->groups()->attach($group);

    expect($draft->is_published)->toBeFalse()
        ->and($draft->content_nl)->toBeNull()
        ->and($draft->content_fr)->toBeNull()
        ->and($draft->location)->toBeNull();
});
