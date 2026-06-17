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
        ->assertSee('Parent Group') // Parent (invisible) is the region grouping header, not a list entry
        ->assertDontSee('Invisible Group 1');
});

test('groups index leads with the finder: region selector and the visible gemeente list', function () {
    $brussel = Group::create(['shortname' => 'bxl', 'name' => 'Brussel', 'invisible' => true, 'started_at' => now()]);
    Group::create(['shortname' => 'sb', 'name' => 'Schaarbeek', 'parent_id' => $brussel->id, 'invisible' => false, 'started_at' => now()]);
    Group::create(['shortname' => 'and', 'name' => 'Anderlecht', 'parent_id' => $brussel->id, 'invisible' => false, 'started_at' => now()]);

    get(route('groups.index'))
        ->assertOk()
        ->assertSee('Jouw buurt fietst al, rij mee.')
        ->assertSee('Heel België')              // default region selector button
        ->assertSee('Schaarbeek')               // visible gemeente listed as a card
        ->assertSee('Anderlecht')
        ->assertViewHas('groups', fn ($groups) => $groups->count() === 2);
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
        'duration_minutes' => 60,
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
        'duration_minutes' => 60,
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
        'duration_minutes' => 60,
        'location' => 'Past place',
        'author_id' => $author->id,
    ]);
    $pastActivity->groups()->attach($child);

    $response = get(route('groups.show', $child));

    // News was CUT from the chapter page (Critique v3) — only the typed activity agenda remains.
    $response->assertOk()
        ->assertSee('Nearest Child Activity') // hero title (next ride, incl. parent/region)
        ->assertSee('Child place')            // hero meeting point
        ->assertSee('Parent place')           // later ride listed in the agenda
        ->assertDontSee('Past Child Activity')
        ->assertDontSee('Articles from Parent Groups')
        ->assertDontSee('Activities from Parent Groups')
        ->assertDontSee('Uit de buurt');      // national news no longer on the chapter page

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

test('chapter home leads with the next ride in NL, not metadata', function () {
    $author = User::factory()->create();
    $group = Group::create(['shortname' => 'sb', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);

    $next = Activity::create([
        'title_nl' => 'Kidical Mass Schaarbeek',
        'title_fr' => 'Kidical Mass Schaerbeek',
        'content_nl' => 'x', 'content_fr' => 'x',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addWeek(),
        'duration_minutes' => 60,
        'location' => 'Place Colignon',
        'author_id' => $author->id,
    ]);
    $next->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Op de agenda')           // unified typed agenda; the ride lists as a normal day-grouped row
        ->assertSee('Place Colignon')         // the ride's venue shows in its ride-row meta
        ->assertDontSee('Naar de rit')        // no featured spotlight hero on the chapter page anymore
        ->assertDontSee('Part of:')
        ->assertDontSee('Organised by')
        ->assertDontSee('Subgroups');
});

test('chapter home shows a designed empty state when no upcoming ride', function () {
    $group = Group::create(['shortname' => 'nm', 'name' => 'Kidical Mass Namur', 'zip' => '5000', 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Nog geen fietstocht gepland')
        ->assertSee('Blijf op de hoogte');
});

test('chapter team carousel shows member cards with first names and roles', function () {
    $group = Group::create(['shortname' => 'sb2', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    $sofie = User::factory()->create(['name' => 'Sofie Maes']);
    $group->users()->attach($sofie);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Wij zwaaien je welkom aan de start') // headline stays
        ->assertSee('chapter-team__card')                 // polaroid card rendered
        ->assertSee('Sofie')                              // first name on the card
        ->assertSee('trekker')                            // role as plain text
        ->assertSee('img/illustrations/')                 // illustration placeholder in the photo slot
        ->assertDontSee('Organiser')                      // never the cold chip
        ->assertDontSee('chapter-team__avatar');          // old initials avatar is gone
});

test('chapter agenda labels a workshop as a workshop, never as a ride', function () {
    $author = User::factory()->create();
    $group = Group::create(['shortname' => 'and', 'name' => 'Kidical Mass Anderlecht', 'zip' => '1070', 'invisible' => false, 'started_at' => now()]);

    $workshop = Activity::create([
        'title_nl' => 'Fietscheck en sleutelworkshop', 'title_fr' => 'x',
        'content_nl' => 'x', 'content_fr' => 'x',
        'activity_type' => 'workshop',
        'begin_date' => now()->addDays(3), 'duration_minutes' => 120,
        'location' => 'Cyclo werkplaats', 'author_id' => $author->id,
    ]);
    $workshop->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        // No ride → the warm empty-ride state, NOT the workshop dressed up as a ride.
        ->assertSee('Nog geen fietstocht gepland')
        // The workshop shows in the agenda, correctly typed.
        ->assertSee('Workshop')
        ->assertSee('Fietscheck en sleutelworkshop')
        // A workshop never gets the ride CTA.
        ->assertDontSee('Naar de fietstocht');
});

test('chapter agenda labels a meeting with a Vergadering chip', function () {
    $author = User::factory()->create();
    $group = Group::create(['shortname' => 'bxl', 'name' => 'Kidical Mass Brussel Stad', 'zip' => '1000', 'invisible' => false, 'started_at' => now()]);

    $meeting = Activity::create([
        'title_nl' => 'Vrijwilligersmeeting', 'title_fr' => 'x',
        'content_nl' => 'x', 'content_fr' => 'x',
        'activity_type' => 'meeting',
        'begin_date' => now()->addDays(2), 'duration_minutes' => 90,
        'location' => 'Mundo-B', 'author_id' => $author->id,
    ]);
    $meeting->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Vergadering')             // ride-row chip for meetings (was "Voor vrijwilligers" in old agenda-item)
        ->assertSee('Vrijwilligersmeeting')
        ->assertDontSee('Naar de fietstocht');
});

test('chapter home hides the news block when there is no news', function () {
    $group = Group::create(['shortname' => 'sb3', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('Uit de buurt');
});
