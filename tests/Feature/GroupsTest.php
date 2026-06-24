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
        ->assertSee('Volgende fietsparade')                          // the §2 next-ride card leads
        ->assertSee(route('activities.show', $nearestActivity), false) // it features the NEAREST ride (incl. parent/region)
        ->assertSee('Child place')            // the nearest ride's meeting point, in the card
        ->assertSee(route('activities.show', $laterActivity), false) // later ride listed in the §3 parades strip (pill links to it; venue no longer shown there)
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
        ->assertSee('Volgende fietsparade')                  // §2 card eyebrow — parade leads the page
        ->assertSee('Place Colignon')                        // the ride's venue
        ->assertSee('Bekijk deze parade')                    // the click-through affordance cue
        ->assertSee(route('activities.show', $next), false)  // the whole card links to the ride detail
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

test('chapter nav shows the chapter postcode just right of the logo', function () {
    $group = Group::create(['shortname' => 'sb-pc', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('site-nav__postcode')
        ->assertSeeInOrder(['site-nav__logo', 'site-nav__postcode', '1030'], false); // postcode follows the logo in source order
});

test('chapter nav omits the postcode when the chapter has no zip', function () {
    $group = Group::create(['shortname' => 'no-zip', 'name' => 'Kidical Mass Ergens', 'zip' => null, 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('site-nav__postcode');
});

test('the nav postcode appears only on chapter pages, not the chapter index', function () {
    Group::create(['shortname' => 'idx-pc', 'name' => 'Kidical Mass Gent', 'zip' => '9000', 'invisible' => false, 'started_at' => now()]);

    get(route('groups.index'))
        ->assertOk()
        ->assertDontSee('site-nav__postcode');
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

test('the team carousel seats a "Jij?" invite between the captains and the crew', function () {
    $group = Group::create(['shortname' => 'sb3', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    $group->users()->attach(User::factory()->create(['name' => 'Sofie Maes'])); // a trekker (captain)

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('chapter-team__card--cta')   // the invite card rides in the row
        ->assertSee('Jij?')                       // the reader's own seat
        ->assertSee('kom erbij')                  // its role line
        ->assertSee('href="#aanmelden"', false)   // links down to the §7 sign-up band
        ->assertSee('cyclist-peace-sign')         // the volunteer illustration fills the slot
        // Captain (trekker) leads, the invite sits at the seam, then the crew (roze hesje).
        ->assertSeeInOrder(['Sofie', 'Jij?', 'Marieke'])
        ->assertDontSee('Kom je bij hen staan');  // the old loose invite line is dropped
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
        // The workshop title appears in the "Ook in {gemeente}" rail.
        ->assertSee('Fietscheck en sleutelworkshop')
        ->assertSee('Ook in')
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
        // The meeting title appears in the "Ook in {gemeente}" rail (reuses <x-ride-row>;
        // the title carries the kind, so there's no separate NL type label any more).
        ->assertSee('Vrijwilligersmeeting')
        ->assertSee('Ook in')
        // Never the English enum label, and never the ride CTA.
        ->assertDontSee('Meeting')
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
        ->assertSee('Met dank aan')
        ->assertSee('Fietsbieb Anderlecht')
        ->assertDontSee('Verborgen Partner');
});

test('chapter page no longer shows press — it moved to the channel Press page', function () {
    $group = Group::create(['shortname' => 'mol', 'name' => 'Kidical Mass Mol', 'zip' => '2400', 'invisible' => false, 'started_at' => now()]);

    $article = PressArticle::factory()->create([
        'title_nl' => 'Gezinnen fietsen door Mol',
        'title_fr' => 'x',
        'outlet' => 'Het Nieuwsblad',
        'url' => null,
        'published_at' => now()->subMonths(2),
    ]);
    $article->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('In de pers')
        ->assertDontSee('Het Nieuwsblad')
        ->assertDontSee('Gezinnen fietsen door Mol');
});

test('chapter extras section hidden when no partners', function () {
    $group = Group::create(['shortname' => 'hm', 'name' => 'Kidical Mass Hasselt', 'zip' => '3500', 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('Met dank aan')
        ->assertDontSee('Downloads'); // downloads ride along with real partners, never alone
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

test('press articles no longer link out on the chapter page — press moved to channel Press page', function () {
    $group = Group::create(['shortname' => 'ev', 'name' => 'Kidical Mass Evere', 'zip' => '1140', 'invisible' => false, 'started_at' => now()]);

    $linked = PressArticle::factory()->create(['title_nl' => 'Met een link erbij', 'outlet' => 'BRUZZ', 'url' => 'https://example.test/artikel', 'published_at' => now()->subMonth()]);
    $plain = PressArticle::factory()->create(['title_nl' => 'Zonder link', 'outlet' => 'BX1', 'url' => null, 'published_at' => now()->subMonths(2)]);
    $linked->groups()->attach($group);
    $plain->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('https://example.test/artikel', false)
        ->assertDontSee('Zonder link');
});

test('press article ordering is irrelevant — press no longer appears on the chapter page', function () {
    $group = Group::create(['shortname' => 'jt', 'name' => 'Kidical Mass Jette', 'zip' => '1090', 'invisible' => false, 'started_at' => now()]);

    $older = PressArticle::factory()->create(['title_nl' => 'Oud bericht', 'outlet' => 'Le Soir', 'url' => null, 'published_at' => now()->subYear()]);
    $newer = PressArticle::factory()->create(['title_nl' => 'Vers bericht', 'outlet' => 'De Standaard', 'url' => null, 'published_at' => now()->subWeek()]);
    $older->groups()->attach($group);
    $newer->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('Oud bericht')
        ->assertDontSee('Vers bericht');
});

test('downloads block appears alongside real partners', function () {
    $group = Group::create(['shortname' => 'wm', 'name' => 'Kidical Mass Watermaal', 'zip' => '1170', 'invisible' => false, 'started_at' => now()]);

    Partner::factory()->create(['group_id' => $group->id, 'name' => 'Lokale Bakker', 'visible' => true, 'show_logo' => false]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Met dank aan')
        ->assertSee('Downloads');
});

test('guest follow box shows the newsletter opt-in in §2', function () {
    // The standalone chapter-optin band (show-join="true") is removed in v4;
    // the subscribe CTA now lives inside §2 with show-join="false".
    $group = Group::create(['shortname' => 'fo', 'name' => 'Kidical Mass Vorst', 'zip' => '1190', 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Mis geen rit')                      // subscribe CTA in §2
        ->assertSee('Schrijf je in')                     // primary action
        ->assertDontSee('help mee als vrijwilliger');    // join bridge removed (show-join="false")
});

test('logged-in follow box shows a calm non-dead-end state in §2', function () {
    // The standalone chapter-optin band (show-join="true") is removed in v4;
    // show-join="false" means the plain "Je bent al mee" branch renders in §2.
    $group = Group::create(['shortname' => 'st', 'name' => 'Kidical Mass Sint-Gillis', 'zip' => '1060', 'invisible' => false, 'started_at' => now()]);

    actingAs(User::factory()->create())
        ->get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Je bent al mee')               // plain status line for logged-in user
        ->assertDontSee('Meer dan meefietsen?')     // no escalation push in §2 (show-join=false)
        // Regression: "Word vrijwilliger" belongs only to the show-join=true optin branch;
        // it must not bleed into the calm logged-in state. Note: "Voorkeuren beheren" /
        // "Beheer voorkeuren" are NOT guarded here — "Beheer voorkeuren" is the intended
        // settings link rendered by the show-join=false @auth branch.
        ->assertDontSee('Word vrijwilliger');       // optin escalation button absent (show-join=false)
});

test('chapter gallery shows the latest past ride photos under a grounded lockup', function () {
    Storage::fake('media');
    $group = Group::create(['shortname' => 'sbg', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    pastRideFor($group, 'Bright Light Parade', now()->subWeeks(2), photos: 3);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Recentste parade')        // the poster heading names the band (top-left)
        ->assertSee('ride-gallery__feature-cal') // the calendar tear-off (date it was), now under the title
        ->assertSee('ride-gallery__tile');   // photo tiles render on the wall
});

test('the opt-in card is woven into the gallery wall — subscribe for guests', function () {
    Storage::fake('media');
    $group = Group::create(['shortname' => 'sbg-opt', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    pastRideFor($group, 'Bright Light Parade', now()->subWeeks(2), photos: 3);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('ride-gallery__optin')    // the card spans cols 3–4 inside the wall
        ->assertSee('Mis geen rit')           // guests get the subscribe teaser
        ->assertSee('Schrijf je in')
        ->assertDontSee('help mee als vrijwilliger'); // the join bridge line is gone
});

test('the gallery opt-in escalates a logged-in follower to volunteer', function () {
    Storage::fake('media');
    $group = Group::create(['shortname' => 'sbg-vol', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    pastRideFor($group, 'Bright Light Parade', now()->subWeeks(2), photos: 3);

    actingAs(User::factory()->create())
        ->get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('ride-gallery__optin')
        ->assertSee('Word vrijwilliger')   // signed-in followers get the volunteer ask
        ->assertDontSee('Schrijf je in');  // no subscribe CTA once you're in
});

test('chapter gallery caps the wall on a full row (nine tiles, the last five XL-only) while the lightbox keeps the full set', function () {
    Storage::fake('media');
    $group = Group::create(['shortname' => 'sbg2', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    pastRideFor($group, 'Grote Kidical Mass', now()->subWeek(), photos: 11);

    $content = get(route('groups.show', $group))->assertOk()->getContent();

    // The grid ends on a full row: poster + the 1-col opt-in card leave room for nine tiles
    // (three rows) on the XL wall — never a ragged half-row.
    expect(substr_count($content, 'ride-gallery__tile'))->toBe(9);
    // ... the last five of which only appear on the widest (4-column) wall; below it the
    // calmer 2/3-column wall shows just the first four.
    expect(substr_count($content, 'ride-gallery__cell--xl'))->toBe(5);
    // ... and the photos past the wall stay reachable through the lightbox set.
    expect($content)->toContain('Foto 10')->toContain('Foto 11');
});

test('chapter gallery drops to five tiles when there are not enough to fill the nine-tile wall', function () {
    Storage::fake('media');
    $group = Group::create(['shortname' => 'sbg2b', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    // 8 photos: poster + 7 candidates. Seven can't fill the nine-tile wall cleanly, so it
    // falls back to a clean five-tile block (the "remove the ragged corner" rule).
    pastRideFor($group, 'Lenteparade', now()->subWeek(), photos: 8);

    $content = get(route('groups.show', $group))->assertOk()->getContent();

    expect(substr_count($content, 'ride-gallery__tile'))->toBe(5);
    expect(substr_count($content, 'ride-gallery__cell--xl'))->toBe(1);
    // The unshown photos still live in the lightbox set.
    expect($content)->toContain('Foto 8');
});

test('the lightbox carries usable controls — counter, edge-pinned nav, focus restore and a live label', function () {
    Storage::fake('media');
    $group = Group::create(['shortname' => 'sbg-lb', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    pastRideFor($group, 'Lichtparade', now()->subWeek(), photos: 4);

    $content = get(route('groups.show', $group))->assertOk()->getContent();

    expect($content)
        // A "n / total" counter orients the viewer within the set.
        ->toContain('ride-gallery__lb-counter')
        ->toContain("(index + 1) + ' / ' + photos.length")
        // Prev/next are reachable refs so Tab can cycle them (focus trap).
        ->toContain('x-ref="prevBtn"')
        ->toContain('x-ref="nextBtn"')
        ->toContain('trapTab($event)')
        // The dialog announces the current photo, not a static label.
        ->toContain("'Foto ' + (index + 1) + ' van ' + photos.length")
        // Opening remembers its trigger so focus returns to it on close.
        ->toContain('this.trigger?.focus()')
        ->toContain('open(0, $event)')
        // Swipe navigation for touch.
        ->toContain('onTouchEnd($event)');
});

test('the lightbox expresses brand enthusiasm — fly-from-tile open, directional flips and a rotating palette accent', function () {
    Storage::fake('media');
    $group = Group::create(['shortname' => 'sbg-joy', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    pastRideFor($group, 'Belparade', now()->subWeek(), photos: 4);

    $content = get(route('groups.show', $group))->assertOk()->getContent();

    expect($content)
        // The photo launches from the clicked tile's position.
        ->toContain('getBoundingClientRect()')
        ->toContain('this.entering = true')
        // Navigation is directional (slide), not a hard cut.
        ->toContain('navigate(1)')
        ->toContain('navigate(-1)')
        ->toContain('var(--lb-slide)')
        // The accent rotates through the brand palette as you flip.
        ->toContain('--color-kidical-green')
        ->toContain("'--lb-accent: var(' + accents[index % accents.length] + ')'")
        // Motion stays reduced-motion safe.
        ->toContain("window.matchMedia('(prefers-reduced-motion: reduce)')");
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

test('chapter hero is mission intro only: no stats, no press', function () {
    $group = Group::create(['shortname' => 'sb', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()->subYears(3)]);
    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Wij fietsen samen met kinderen door Schaarbeek')
        ->assertDontSee('ritten sinds')   // micro-proof is NOT in the hero
        ->assertDontSee('In de pers');    // no press trust line in the hero
});

test('controller buckets upcoming rides and other activities separately', function () {
    $author = User::factory()->create();
    $group = Group::create(['shortname' => 'sb', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()->subYears(3)]);

    $ride = Activity::create(['title_nl' => 'Parade juni', 'title_fr' => 'x', 'content_nl' => 'x', 'content_fr' => 'x', 'activity_type' => 'kidicalmass', 'begin_date' => now()->addWeek(), 'duration_minutes' => 60, 'location' => 'Place Colignon', 'author_id' => $author->id]);
    $workshop = Activity::create(['title_nl' => 'Sleutelworkshop', 'title_fr' => 'x', 'content_nl' => 'x', 'content_fr' => 'x', 'activity_type' => 'workshop', 'begin_date' => now()->addDays(3), 'duration_minutes' => 90, 'location' => 'Werkplaats', 'author_id' => $author->id]);
    $pastRide = Activity::create(['title_nl' => 'Parade mei', 'title_fr' => 'x', 'content_nl' => 'x', 'content_fr' => 'x', 'activity_type' => 'kidicalmass', 'begin_date' => now()->subMonth(), 'duration_minutes' => 60, 'location' => 'Place Colignon', 'author_id' => $author->id]);
    $ride->groups()->attach($group);
    $workshop->groups()->attach($group);
    $pastRide->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertViewHas('upcomingRides', fn ($r) => $r->count() === 1 && $r->first()->is($ride))
        ->assertViewHas('otherActivities', fn ($o) => $o->count() === 1 && $o->first()->is($workshop))
        ->assertViewHas('pastRidesCount', 1);
});

test('chapter shows the real track-record line (sinds + parades), no fake numbers', function () {
    // The proof stats moved out of §2 to §6b (under the team) in the v4 next-ride rebuild,
    // where they read as the crew's track record. They render even without a team roster.
    $author = User::factory()->create();
    $group = Group::create(['shortname' => 'sb', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()->setDate(2023, 1, 1)]);
    foreach ([now()->subMonths(2), now()->subMonth()] as $when) {
        $r = Activity::create(['title_nl' => 'Parade', 'title_fr' => 'x', 'content_nl' => 'x', 'content_fr' => 'x', 'activity_type' => 'kidicalmass', 'begin_date' => $when, 'duration_minutes' => 60, 'location' => 'Place Colignon', 'author_id' => $author->id]);
        $r->groups()->attach($group);
    }
    $next = Activity::create(['title_nl' => 'Parade', 'title_fr' => 'x', 'content_nl' => 'x', 'content_fr' => 'x', 'activity_type' => 'kidicalmass', 'begin_date' => now()->addWeek(), 'duration_minutes' => 60, 'location' => 'Place Colignon', 'author_id' => $author->id]);
    $next->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('sinds 2023')
        ->assertSee('op pad in')
        ->assertSee('2 parades')          // two past rides counted
        ->assertSee('gereden')
        ->assertDontSee('gezinnen vorige keer');  // no invented attendance figure (e.g. "± 80 gezinnen vorige keer")
});

test('the track-record stats sit under the team carousel as the crew accomplishments', function () {
    $author = User::factory()->create();
    $group = Group::create(['shortname' => 'sbt', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()->setDate(2021, 1, 1)]);
    $group->users()->attach(User::factory()->create(['name' => 'Sofie Maes']));
    $past = Activity::create(['title_nl' => 'Parade', 'title_fr' => 'x', 'content_nl' => 'x', 'content_fr' => 'x', 'activity_type' => 'kidicalmass', 'begin_date' => now()->subMonth(), 'duration_minutes' => 60, 'location' => 'Place Colignon', 'author_id' => $author->id]);
    $past->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        // The team headline comes first, then the track-record line — the numbers read as theirs.
        ->assertSeeInOrder(['Wij zwaaien je welkom aan de start', 'sinds 2021'], false);
});

test('the §2 ride card is a single affordance, with no subscribe line nested or beneath it', function () {
    // v4: the §2 decoupled subscribe line is gone — the subscribe ask now lives in the
    // photo wall opt-in. §2 is purely the next-ride card (its own single link).
    $author = User::factory()->create();
    $group = Group::create(['shortname' => 'nr', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    $ride = Activity::create([
        'title_nl' => 'Parade', 'title_fr' => 'x', 'content_nl' => 'Een rustige lus.', 'content_fr' => 'x',
        'activity_type' => 'kidicalmass', 'begin_date' => now()->addWeek(), 'duration_minutes' => 60,
        'location' => 'Gemeenteplein Colignon', 'distance' => '3 km', 'author_id' => $author->id,
    ]);
    $ride->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('3 km')                                  // the real, optional distance string shows
        ->assertSee('Bekijk deze parade')                    // the card's own single affordance
        ->assertDontSee('Hou me op de hoogte van de volgende') // the old decoupled subscribe line is gone
        ->assertDontSee('Kan je er niet bij deze keer?');
});

test('§7 join block defaults to collapsed state without intent param', function () {
    $group = Group::create(['shortname' => 'rev1', 'name' => 'Kidical Mass Roeselare', 'zip' => '8800', 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('open: false', false)         // x-data initialises closed
        ->assertSee('Help mee in Roeselare')       // CTA heading is present
        ->assertSee('Een paar uur per maand');     // warm sub-line present
});

test('§7 join block auto-opens when intent=volunteer is in the query string', function () {
    $group = Group::create(['shortname' => 'rev2', 'name' => 'Kidical Mass Roeselare', 'zip' => '8800', 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $group).'?intent=volunteer')
        ->assertOk()
        ->assertSee('open: true', false);          // x-data initialises open
});

test('chapter team carousel hides members who opted out of the public roster', function () {
    $group = Group::create(['shortname' => 'opt', 'name' => 'Kidical Mass Etterbeek', 'zip' => '1040', 'invisible' => false, 'started_at' => now()]);
    $group->users()->attach(User::factory()->create(['name' => 'Sofie Maes']), ['is_public' => true]);
    $group->users()->attach(User::factory()->create(['name' => 'Bram Verhaeghe']), ['is_public' => false]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Sofie')        // public member shown
        ->assertDontSee('Bram');    // opted-out member hidden
});
