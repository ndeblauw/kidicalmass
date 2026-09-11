<?php

use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use App\Models\Partner;
use App\Models\User;
use Carbon\Carbon;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('invisible field defaults to false', function () {
    $group = Group::create([
        'shortname' => 'test',
        'name' => 'Test Group',
        'started_at' => now(),
    ]);

    $group->refresh();

    expect($group->invisible)->toBeFalse();
});

test('groups index only shows visible groups, grouped under their region node', function () {
    $parent = Group::create(['shortname' => 'parent', 'name' => 'Parent Group', 'invisible' => true, 'started_at' => now()]);
    Group::create(['shortname' => 'visible1', 'name' => 'Visible Group 1', 'parent_id' => $parent->id, 'invisible' => false, 'started_at' => now()]);
    Group::create(['shortname' => 'invisible1', 'name' => 'Invisible Group 1', 'parent_id' => $parent->id, 'invisible' => true, 'started_at' => now()]);

    get(route('groups.index'))
        ->assertOk()
        ->assertViewHas('groups', fn ($groups) => $groups->pluck('name')->all() === ['Visible Group 1'])
        ->assertViewHas('regionCounts', fn ($counts) => $counts['Parent Group'] === 1)
        ->assertDontSee('Invisible Group 1');
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
        'is_published' => true,
        'published_at' => now()->subDays(3),
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
        'is_published' => true,
        'published_at' => now()->subDay(),
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
        'duration_minutes' => 60,
        'location' => 'Parent place',
        'author_id' => $author->id,
        'is_published' => true,
    ]);
    $laterActivity->groups()->attach($parent);

    $nearestActivity = Activity::create([
        'title_nl' => 'Nearest Child Activity',
        'title_fr' => 'Nearest Child Activity',
        'content_nl' => 'Nearest child activity',
        'content_fr' => 'Nearest child activity',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addDay(),
        'duration_minutes' => 60,
        'location' => 'Child place',
        'author_id' => $author->id,
        'is_published' => true,
    ]);
    $nearestActivity->groups()->attach($child);

    $pastActivity = Activity::create([
        'title_nl' => 'Past Child Activity',
        'title_fr' => 'Past Child Activity',
        'content_nl' => 'Past child activity',
        'content_fr' => 'Past child activity',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->subDay(),
        'duration_minutes' => 60,
        'location' => 'Past place',
        'author_id' => $author->id,
        'is_published' => true,
    ]);
    $pastActivity->groups()->attach($child);

    $response = get(route('groups.show', $child))->assertOk();

    $response->assertViewHas('articles', fn ($articles) => $articles->pluck('title_nl')->values()->all() === [
        'Newer Child News',
        'Older Parent News',
    ]);

    $response->assertViewHas('activities', fn ($activities) => $activities->pluck('title_nl')->values()->all() === [
        'Nearest Child Activity',
        'Later Parent Activity',
    ]);

    Carbon::setTestNow();
});

test('chapter detail 404s for invisible region nodes', function () {
    $region = Group::create(['shortname' => 'bxl', 'name' => 'Brussels Capital Region', 'invisible' => true, 'started_at' => now()]);
    $chapter = Group::create(['shortname' => 'sb', 'name' => 'Schaarbeek', 'zip' => '1030', 'parent_id' => $region->id, 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $region))->assertNotFound();
    get(route('groups.show', $chapter))->assertOk();
});

test('controller buckets upcoming rides and other activities separately', function () {
    $author = User::factory()->create();
    $group = Group::create(['shortname' => 'sb', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()->subYears(3)]);

    $ride = Activity::create(['title_nl' => 'Parade juni', 'title_fr' => 'x', 'content_nl' => 'x', 'content_fr' => 'x', 'activity_type' => 'kidicalmass', 'begin_date' => now()->addWeek(), 'duration_minutes' => 60, 'location' => 'Place Colignon', 'author_id' => $author->id, 'is_published' => true]);
    $workshop = Activity::create(['title_nl' => 'Sleutelworkshop', 'title_fr' => 'x', 'content_nl' => 'x', 'content_fr' => 'x', 'activity_type' => 'workshop', 'begin_date' => now()->addDays(3), 'duration_minutes' => 90, 'location' => 'Werkplaats', 'author_id' => $author->id, 'is_published' => true]);
    $pastRide = Activity::create(['title_nl' => 'Parade mei', 'title_fr' => 'x', 'content_nl' => 'x', 'content_fr' => 'x', 'activity_type' => 'kidicalmass', 'begin_date' => now()->subMonth(), 'duration_minutes' => 60, 'location' => 'Place Colignon', 'author_id' => $author->id, 'is_published' => true]);
    $ride->groups()->attach($group);
    $workshop->groups()->attach($group);
    $pastRide->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertViewHas('upcomingRides', fn ($r) => $r->count() === 1 && $r->first()->is($ride))
        ->assertViewHas('otherActivities', fn ($o) => $o->count() === 1 && $o->first()->is($workshop))
        ->assertViewHas('pastRidesCount', 1);
});

test('chapter page shows only visible local partners', function () {
    $group = Group::create(['shortname' => 'and2', 'name' => 'Kidical Mass Anderlecht', 'zip' => '1070', 'invisible' => false, 'started_at' => now()]);

    Partner::factory()->create(['group_id' => $group->id, 'name' => 'Fietsbieb Anderlecht', 'visible' => true, 'show_logo' => false]);
    Partner::factory()->create(['group_id' => $group->id, 'name' => 'Verborgen Partner', 'visible' => false, 'show_logo' => false]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Fietsbieb Anderlecht')
        ->assertDontSee('Verborgen Partner');
});

test('chapter page never shows national (groupless) partners', function () {
    $group = Group::create(['shortname' => 'lv', 'name' => 'Kidical Mass Leuven', 'zip' => '3000', 'invisible' => false, 'started_at' => now()]);

    Partner::factory()->create(['group_id' => $group->id, 'name' => 'Lokale Fietsvriend', 'visible' => true, 'show_logo' => false]);
    Partner::factory()->create(['group_id' => null, 'name' => 'Nationale Koepelpartner', 'visible' => true, 'show_logo' => false]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Lokale Fietsvriend')
        ->assertDontSee('Nationale Koepelpartner');
});

test('chapter team carousel hides members who opted out of the public roster', function () {
    $group = Group::create(['shortname' => 'opt', 'name' => 'Kidical Mass Etterbeek', 'zip' => '1040', 'invisible' => false, 'started_at' => now()]);
    $group->users()->attach(User::factory()->create(['name' => 'Sofie Maes']), ['is_public' => true]);
    $group->users()->attach(User::factory()->create(['name' => 'Bram Verhaeghe']), ['is_public' => false]);

    actingAs(User::factory()->create())
        ->get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Sofie')
        ->assertDontSee('Bram');
});