<?php

use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;

use function Pest\Laravel\get;

beforeEach(function () {
    // Ensure the database is clean before each test
    Group::query()->delete();
});

test('visible scope filters invisible groups', function () {
    // Create visible groups
    $visibleGroup1 = Group::create([
        'shortname' => 'visible1',
        'name' => 'Visible Group 1',
        'invisible' => false,
        'started_at' => now(),
    ]);

    $visibleGroup2 = Group::create([
        'shortname' => 'visible2',
        'name' => 'Visible Group 2',
        'invisible' => false,
        'started_at' => now(),
    ]);

    // Create invisible groups
    Group::create([
        'shortname' => 'invisible1',
        'name' => 'Invisible Group 1',
        'invisible' => true,
        'started_at' => now(),
    ]);

    Group::create([
        'shortname' => 'invisible2',
        'name' => 'Invisible Group 2',
        'invisible' => true,
        'started_at' => now(),
    ]);

    // Test visible scope
    $visibleGroups = Group::visible()->get();

    expect($visibleGroups)->toHaveCount(2)
        ->and($visibleGroups->pluck('id')->toArray())->toContain($visibleGroup1->id, $visibleGroup2->id);
});

test('groups index only shows visible groups', function () {
    // Create a parent group (invisible)
    $parent = Group::create([
        'shortname' => 'parent',
        'name' => 'Parent Group',
        'invisible' => true,
        'started_at' => now(),
    ]);

    // Create visible child groups
    Group::create([
        'shortname' => 'visible1',
        'name' => 'Visible Group 1',
        'parent_id' => $parent->id,
        'invisible' => false,
        'started_at' => now(),
    ]);

    // Create invisible child groups
    Group::create([
        'shortname' => 'invisible1',
        'name' => 'Invisible Group 1',
        'parent_id' => $parent->id,
        'invisible' => true,
        'started_at' => now(),
    ]);

    // Visit groups index
    $response = get(route('groups.index'));

    $response->assertOk()
        ->assertSee('Visible Group 1')
        ->assertSee('Part of:') // Parent name is shown but only as a reference
        ->assertDontSee('Invisible Group 1');
});

test('invisible field defaults to false', function () {
    $group = Group::create([
        'shortname' => 'test',
        'name' => 'Test Group',
        'started_at' => now(),
    ]);

    // Refresh to get the database default
    $group->refresh();

    expect($group->invisible)->toBeFalse();
});

test('group show mixes parent and direct content with correct ordering', function () {
    Carbon::setTestNow('2026-02-21 10:00:00');

    $author = User::factory()->create();

    $parent = Group::create([
        'shortname' => 'parent-group',
        'name' => 'Parent Group',
        'invisible' => false,
        'started_at' => now(),
    ]);

    $child = Group::create([
        'shortname' => 'child-group',
        'name' => 'Child Group',
        'parent_id' => $parent->id,
        'invisible' => false,
        'started_at' => now(),
    ]);

    $olderParentArticle = Article::create([
        'title_nl' => 'Older Parent News',
        'title_fr' => 'Older Parent News',
        'content_nl' => 'Older parent article',
        'content_fr' => 'Older parent article',
        'author_id' => $author->id,
        'created_at' => now()->subDays(3),
        'updated_at' => now()->subDays(3),
    ]);
    $olderParentArticle->groups()->attach($parent);

    $newerChildArticle = Article::create([
        'title_nl' => 'Newer Child News',
        'title_fr' => 'Newer Child News',
        'content_nl' => 'Newer child article',
        'content_fr' => 'Newer child article',
        'author_id' => $author->id,
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);
    $newerChildArticle->groups()->attach($child);

    $laterActivity = Activity::create([
        'title_nl' => 'Later Parent Activity',
        'title_fr' => 'Later Parent Activity',
        'content_nl' => 'Later parent activity',
        'content_fr' => 'Later parent activity',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addDays(5),
        'end_date' => now()->addDays(5)->addHour(),
        'location' => 'Parent place',
        'author_id' => $author->id,
    ]);
    $laterActivity->groups()->attach($parent);

    $nearestActivity = Activity::create([
        'title_nl' => 'Nearest Child Activity',
        'title_fr' => 'Nearest Child Activity',
        'content_nl' => 'Nearest child activity',
        'content_fr' => 'Nearest child activity',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addDay(),
        'end_date' => now()->addDay()->addHour(),
        'location' => 'Child place',
        'author_id' => $author->id,
    ]);
    $nearestActivity->groups()->attach($child);

    $pastActivity = Activity::create([
        'title_nl' => 'Past Child Activity',
        'title_fr' => 'Past Child Activity',
        'content_nl' => 'Past child activity',
        'content_fr' => 'Past child activity',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->subDay(),
        'end_date' => now()->subDay()->addHour(),
        'location' => 'Past place',
        'author_id' => $author->id,
    ]);
    $pastActivity->groups()->attach($child);

    $response = get(route('groups.show', $child));

    $response->assertOk()
        ->assertSee('Newer Child News')
        ->assertSee('Older Parent News')
        ->assertSee('Nearest Child Activity')
        ->assertSee('Later Parent Activity')
        ->assertDontSee('Past Child Activity')
        ->assertDontSee('Articles from Parent Groups')
        ->assertDontSee('Activities from Parent Groups');

    $response->assertViewHas('articles', function ($articles) {
        return $articles->pluck('title_nl')->values()->all() === [
            'Newer Child News',
            'Older Parent News',
        ];
    });

    $response->assertViewHas('activities', function ($activities) {
        return $activities->pluck('title_nl')->values()->all() === [
            'Nearest Child Activity',
            'Later Parent Activity',
        ];
    });

    Carbon::setTestNow();
});
