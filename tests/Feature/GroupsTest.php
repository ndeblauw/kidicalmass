<?php

use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use App\Models\Partner;
use App\Models\PressArticle;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

/**
 * Attach $count fake photos to a ride's `gallery` collection. Tiny GD images on a
 * faked disk so conversions stay instant and nothing litters real storage.
 */
function seedRideGallery(Activity $ride, int $count): void
{
    for ($i = 1; $i <= $count; $i++) {
        $ride->addMedia(UploadedFile::fake()->image("rit-foto-{$i}.jpg", 40, 30))
            ->usingName("Foto {$i}")
            ->toMediaCollection('gallery');
    }
}

/**
 * A past Kidical Mass ride for $group, optionally with $photos gallery photos.
 */
function pastRideFor(Group $group, string $title, CarbonInterface $when, int $photos = 0): Activity
{
    $ride = Activity::create([
        'title_nl' => $title,
        'title_fr' => $title,
        'content_nl' => 'x', 'content_fr' => 'x',
        'activity_type' => 'kidicalmass',
        'begin_date' => $when,
        'duration_minutes' => 60,
        'location' => 'Place Colignon',
        'author_id' => User::factory()->create()->id,
    ]);
    $ride->groups()->attach($group);

    if ($photos > 0) {
        seedRideGallery($ride, $photos);
    }

    return $ride;
}

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
        ->assertSee('Mis geen rit');
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
        // The workshop shows in the agenda, typed by its green calendar accent.
        ->assertSee('--ride-accent: var(--color-kidical-green)', false)
        ->assertSee('Fietscheck en sleutelworkshop')
        // A workshop never gets the ride CTA.
        ->assertDontSee('Naar de fietstocht');
});

test('chapter agenda accents a meeting blue on its calendar lockup', function () {
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
        ->assertSee('--ride-accent: var(--color-kidical-blue)', false) // type now reads as the blue calendar accent
        ->assertSee('Vrijwilligersmeeting')
        ->assertDontSee('Naar de fietstocht');
});

test('chapter home hides the news block when there is no news', function () {
    $group = Group::create(['shortname' => 'sb3', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('Uit de buurt');
});

test('chapter page shows visible local partners', function () {
    $group = Group::create(['shortname' => 'and2', 'name' => 'Kidical Mass Anderlecht', 'zip' => '1070', 'invisible' => false, 'started_at' => now()]);

    Partner::factory()->create(['group_id' => $group->id, 'name' => 'Fietsbieb Anderlecht', 'visible' => true, 'show_logo' => false]);
    Partner::factory()->create(['group_id' => $group->id, 'name' => 'Verborgen Partner', 'visible' => false, 'show_logo' => false]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Vrienden van de groep')
        ->assertSee('Fietsbieb Anderlecht')
        ->assertDontSee('Verborgen Partner');
});

test('chapter page shows press articles linked to the group', function () {
    $group = Group::create(['shortname' => 'mol', 'name' => 'Kidical Mass Mol', 'zip' => '2400', 'invisible' => false, 'started_at' => now()]);

    $article = PressArticle::factory()->create([
        'title_nl' => 'Gezinnen fietsen door Mol',
        'title_fr' => 'Des familles roulent à Mol',
        'outlet' => 'Het Nieuwsblad',
        'url' => null,
        'published_at' => now()->subMonths(2),
    ]);
    $article->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('In de pers')
        ->assertSee('Het Nieuwsblad')
        ->assertSee('Gezinnen fietsen door Mol');
});

test('chapter extras section hidden when no partners or press articles', function () {
    $group = Group::create(['shortname' => 'hm', 'name' => 'Kidical Mass Hasselt', 'zip' => '3500', 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('Vrienden van de groep')
        ->assertDontSee('In de pers')
        ->assertDontSee('Downloads'); // downloads ride along with real extras, never alone
});

test('chapter friends always render as text, never a logo', function () {
    $group = Group::create(['shortname' => 'kk', 'name' => 'Kidical Mass Koekelberg', 'zip' => '1081', 'invisible' => false, 'started_at' => now()]);

    // show_logo is irrelevant on the chapter page: one kind of friend, always a text
    // link, so volunteer-uploaded artwork never lands here.
    Partner::factory()->create([
        'group_id' => $group->id,
        'name' => 'Buurtwinkel Zonder Logo',
        'visible' => true,
        'show_logo' => true,
    ]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Buurtwinkel Zonder Logo')
        ->assertDontSee('chapter-partners__logo'); // no logo <img> markup exists anymore
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

test('chapter partners are listed alphabetically', function () {
    $group = Group::create(['shortname' => 'br', 'name' => 'Kidical Mass Brugge', 'zip' => '8000', 'invisible' => false, 'started_at' => now()]);

    Partner::factory()->create(['group_id' => $group->id, 'name' => 'Zorgwinkel Brugge', 'visible' => true, 'show_logo' => false]);
    Partner::factory()->create(['group_id' => $group->id, 'name' => 'Atelier Velo Brugge', 'visible' => true, 'show_logo' => false]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSeeInOrder(['Atelier Velo Brugge', 'Zorgwinkel Brugge']);
});

test('press article links out only when it has a url', function () {
    $group = Group::create(['shortname' => 'ev', 'name' => 'Kidical Mass Evere', 'zip' => '1140', 'invisible' => false, 'started_at' => now()]);

    $linked = PressArticle::factory()->create(['title_nl' => 'Met een link erbij', 'outlet' => 'BRUZZ', 'url' => 'https://example.test/artikel', 'published_at' => now()->subMonth()]);
    $plain = PressArticle::factory()->create(['title_nl' => 'Zonder link', 'outlet' => 'BX1', 'url' => null, 'published_at' => now()->subMonths(2)]);
    $linked->groups()->attach($group);
    $plain->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('https://example.test/artikel', false) // the linked title is an <a href>
        ->assertSee('Zonder link');
});

test('press articles are ordered newest first', function () {
    $group = Group::create(['shortname' => 'jt', 'name' => 'Kidical Mass Jette', 'zip' => '1090', 'invisible' => false, 'started_at' => now()]);

    $older = PressArticle::factory()->create(['title_nl' => 'Oud bericht', 'outlet' => 'Le Soir', 'url' => null, 'published_at' => now()->subYear()]);
    $newer = PressArticle::factory()->create(['title_nl' => 'Vers bericht', 'outlet' => 'De Standaard', 'url' => null, 'published_at' => now()->subWeek()]);
    $older->groups()->attach($group);
    $newer->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSeeInOrder(['Vers bericht', 'Oud bericht']);
});

test('downloads block appears alongside real extras', function () {
    $group = Group::create(['shortname' => 'wm', 'name' => 'Kidical Mass Watermaal', 'zip' => '1170', 'invisible' => false, 'started_at' => now()]);

    $article = PressArticle::factory()->create(['title_nl' => 'Lokaal nieuws', 'outlet' => 'BRUZZ', 'url' => null, 'published_at' => now()->subMonth()]);
    $article->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('In de pers')
        ->assertSee('Downloads');
});

test('guest follow box bridges to the volunteer signup', function () {
    $group = Group::create(['shortname' => 'fo', 'name' => 'Kidical Mass Vorst', 'zip' => '1190', 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Schrijf je in')                 // following stays the primary action
        ->assertSee('help mee als vrijwilliger')      // bridge to the join step
        ->assertSee('#aanmelden', false);             // anchors to the join band
});

test('logged-in follow box nudges toward joining rather than dead-ending', function () {
    $group = Group::create(['shortname' => 'st', 'name' => 'Kidical Mass Sint-Gillis', 'zip' => '1060', 'invisible' => false, 'started_at' => now()]);

    actingAs(User::factory()->create())
        ->get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Meer dan meefietsen?') // an enticing title, not a flat status line
        ->assertSee('Word vrijwilliger')    // escalates to the next step
        ->assertDontSee('Voorkeuren beheren') // no settings link cluttering the box
        ->assertDontSee('Beheer voorkeuren'); // the old dead-end button is gone on chapter pages
});

test('chapter gallery shows the latest past ride photos under a grounded lockup', function () {
    Storage::fake('media');
    $group = Group::create(['shortname' => 'sbg', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    pastRideFor($group, 'Bright Light Parade', now()->subWeeks(2), photos: 3);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Recentste parade')      // the lockup eyebrow names the band
        ->assertSee('chapter-latest__rail')  // the calendar tear-off (date it was)
        ->assertSee("3 foto's")              // the photo-count action opens the full set
        ->assertSee('chapter-gallery__tile'); // photo tiles render on the wall
});

test('chapter gallery caps the wall at six tiles (the last two XL-only) while the lightbox keeps the full set', function () {
    Storage::fake('media');
    $group = Group::create(['shortname' => 'sbg2', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    pastRideFor($group, 'Grote Kidical Mass', now()->subWeek(), photos: 9);

    $content = get(route('groups.show', $group))->assertOk()->getContent();

    // The poster takes the first photo; up to six more fill the wall as tiles ...
    expect(substr_count($content, 'chapter-gallery__tile'))->toBe(6);
    // ... the last two of which only appear on the widest (4-column) wall.
    expect(substr_count($content, 'chapter-gallery__cell--xl'))->toBe(2);
    // ... and the eighth and ninth stay reachable through the lightbox set.
    expect($content)->toContain('Foto 8')->toContain('Foto 9');
});

test('chapter gallery follows the most recent past ride, not an older one', function () {
    Storage::fake('media');
    $group = Group::create(['shortname' => 'sbg3', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    $old = pastRideFor($group, 'Oude Rit', now()->subMonths(3), photos: 2);
    $recent = pastRideFor($group, 'Recente Rit', now()->subWeek(), photos: 2);

    // The ride's name is no longer printed, so the calendar tear-off's date is
    // what tells the recent ride from the older one in the poster.
    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('datetime="'.$recent->begin_date->toDateString().'"', false)
        ->assertDontSee('datetime="'.$old->begin_date->toDateString().'"', false);
});

test('chapter gallery ignores upcoming rides even when they carry photos', function () {
    Storage::fake('media');
    $group = Group::create(['shortname' => 'sbg4', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    pastRideFor($group, 'Toekomstige Rit', now()->addWeek(), photos: 4); // future, with photos

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('Laatste rit')        // no past ride → no gallery band
        ->assertDontSee('chapter-latest__rail');
});

test('chapter gallery stays hidden when the latest ride has no photos and the opt-in falls back', function () {
    Storage::fake('media');
    $group = Group::create(['shortname' => 'sbg5', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    pastRideFor($group, 'Rit zonder fotos', now()->subWeek(), photos: 0);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('Laatste rit')
        ->assertDontSee('chapter-gallery__tile')
        ->assertSee('Mis geen rit'); // opt-in falls back under the agenda
});

test('the latest-ride poster carries no inline add-photos link, even for a roze hesje', function () {
    // The poster keeps a single "bekijk alle foto's" action; uploading photos lives in
    // the backstage, not as an inline link on the public wall (Frederik 2026-06-18).
    Storage::fake('media');
    $group = Group::create(['shortname' => 'sbg6', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    pastRideFor($group, 'Recente Rit', now()->subWeek(), photos: 2);

    $hesje = User::factory()->create();
    $group->users()->attach($hesje, ['role' => 'pinkvest']);

    actingAs($hesje)
        ->get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('Voeg je foto')
        ->assertDontSee('chapter-latest__upload');
});

test('guests and ordinary members never see the add-photos button', function () {
    Storage::fake('media');
    $group = Group::create(['shortname' => 'sbg7', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    pastRideFor($group, 'Recente Rit', now()->subWeek(), photos: 2);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('Voeg je foto')
        ->assertDontSee('chapter-latest__upload');

    actingAs(User::factory()->create())
        ->get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('Voeg je foto')
        ->assertDontSee('chapter-latest__upload');
});
