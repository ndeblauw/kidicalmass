<?php

use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;

test('dashboard shows relevant upcoming and past activities', function () {
    Carbon::setTestNow('2026-03-01 10:00:00');

    $user = User::factory()->create();
    $group = Group::factory()->create();
    $otherGroup = Group::factory()->create();
    $user->groups()->attach($group);

    $upcoming = Activity::create([
        'title_nl' => 'Upcoming Group Activity',
        'title_fr' => 'Upcoming Group Activity',
        'content_nl' => 'Upcoming',
        'content_fr' => 'Upcoming',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addDay(),
        'end_date' => now()->addDay()->addHour(),
        'location' => 'Group location',
        'author_id' => $user->id,
    ]);
    $upcoming->groups()->attach($group);

    $past = Activity::create([
        'title_nl' => 'Past Group Activity',
        'title_fr' => 'Past Group Activity',
        'content_nl' => 'Past',
        'content_fr' => 'Past',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->subDay(),
        'end_date' => now()->subDay()->addHour(),
        'location' => 'Group location',
        'author_id' => $user->id,
    ]);
    $past->groups()->attach($group);

    $irrelevant = Activity::create([
        'title_nl' => 'Other Group Activity',
        'title_fr' => 'Other Group Activity',
        'content_nl' => 'Other',
        'content_fr' => 'Other',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addDays(2),
        'end_date' => now()->addDays(2)->addHour(),
        'location' => 'Other location',
        'author_id' => $user->id,
    ]);
    $irrelevant->groups()->attach($otherGroup);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Upcoming Group Activity')
        ->assertSee('Past Group Activity')
        ->assertDontSee('Other Group Activity');

    Carbon::setTestNow();
});

test('group dashboard is only available for members', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $outsider = User::factory()->create();

    $member->groups()->attach($group);

    Activity::create([
        'title_nl' => 'Group Upcoming Activity',
        'title_fr' => 'Group Upcoming Activity',
        'content_nl' => 'Upcoming',
        'content_fr' => 'Upcoming',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addDay(),
        'end_date' => now()->addDay()->addHour(),
        'location' => 'Group location',
        'author_id' => $member->id,
    ])->groups()->attach($group);

    Article::create([
        'title_nl' => 'Group News Item',
        'title_fr' => 'Group News Item',
        'content_nl' => 'News',
        'content_fr' => 'News',
        'author_id' => $member->id,
    ])->groups()->attach($group);

    $this->actingAs($member)
        ->get(route('home.groups.show', $group))
        ->assertSuccessful()
        ->assertSee('Group Upcoming Activity')
        ->assertSee('Group News Item');

    $this->actingAs($outsider)
        ->get(route('home.groups.show', $group))
        ->assertForbidden();
});

test('authenticated user can create an activity through resource route', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $response = $this->actingAs($user)->post(route('activities.store'), [
        'title_nl' => 'New Managed Activity',
        'title_fr' => 'Nouvelle activité',
        'content_nl' => 'Inhoud',
        'content_fr' => 'Contenu',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addDay()->toDateTimeString(),
        'end_date' => now()->addDays(2)->toDateTimeString(),
        'location' => 'Brussels',
        'groups' => [$group->id],
    ]);

    $response->assertRedirect();

    $activity = Activity::query()->where('title_nl', 'New Managed Activity')->firstOrFail();

    expect($activity->author_id)->toBe($user->id)
        ->and($activity->groups->pluck('id')->all())->toContain($group->id);
});
