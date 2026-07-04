<?php

use App\Models\Activity;
use App\Models\Group;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

/**
 * The roze-hesje page (replaces the old backstage): a per-chapter, logged-in-only
 * surface that lives in the public site framework with a roze hero. Gated on chapter
 * membership; the full roster + besloten materials are visible only to fellow hesjes.
 * Plan: docs/wiki/design/30-skeleton/chapters-roze-hesjes.md
 */
function rozeChapter(string $name = 'Kidical Mass Mons', string $shortname = 'mons'): Group
{
    return Group::create([
        'shortname' => $shortname,
        'name' => $name,
        'zip' => '7000',
        'invisible' => false,
        'started_at' => now(),
    ]);
}

test('roze page renders for a logged-in chapter member', function () {
    $group = rozeChapter();
    $member = User::factory()->create(['name' => 'Violette Dupont']);
    $group->users()->attach($member);

    actingAs($member)
        ->get(route('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertSee('Kidical Mass Mons');
});

test('roze page rejects a logged-in non-member', function () {
    $group = rozeChapter();
    $outsider = User::factory()->create();

    actingAs($outsider)
        ->get(route('groups.roze-hesjes', $group))
        ->assertForbidden();
});

test('roze page sends a guest with no demo volunteer to the activate screen', function () {
    $group = rozeChapter();

    get(route('groups.roze-hesjes', $group))
        ->assertRedirect(route('backstage.activate', $group));
});

test('roze page shows the full roster, not just the public opt-ins', function () {
    $group = rozeChapter();
    $lead = User::factory()->create(['name' => 'Violette Dupont']);
    $hidden = User::factory()->create(['name' => 'Karim Benali']);
    $group->users()->attach($lead, ['is_public' => true]);
    $group->users()->attach($hidden, ['is_public' => false]);

    actingAs($lead)
        ->get(route('groups.roze-hesjes.groep', $group))
        ->assertOk()
        ->assertSee('De roze hesjes van Mons')
        ->assertSee('Violette')
        ->assertSee('Karim Benali'); // a non-public member is still visible to fellow hesjes
});

test('roze page lists the upcoming ride in the typed agenda', function () {
    $author = User::factory()->create();
    $group = rozeChapter();
    $member = User::factory()->create();
    $group->users()->attach($member);

    $ride = Activity::create([
        'title_nl' => 'Kidical Mass Mons', 'title_fr' => 'x',
        'content_nl' => 'x', 'content_fr' => 'x',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addWeek(), 'duration_minutes' => 60,
        'location' => 'Théâtre le Manège', 'author_id' => $author->id,
        'is_published' => true,
    ]);
    $ride->groups()->attach($group);

    actingAs($member)
        ->get(route('groups.roze-hesjes.agenda', $group))
        ->assertOk()
        ->assertSee('Théâtre le Manège');
});

test('roze materiaal splits the besloten group from the shareable one', function () {
    $group = rozeChapter();
    $member = User::factory()->create();
    $group->users()->attach($member);

    actingAs($member)
        ->get(route('groups.roze-hesjes.materiaal', $group))
        ->assertOk()
        ->assertSee('Jouw materiaal')
        ->assertSee('Voor de hesjes')      // the besloten section title
        ->assertSee('Afsprakencharter')    // a besloten (hesje-only) document
        ->assertSee('Vrij om te delen')    // the public section title
        ->assertSee('Playlist')            // now shareable, sits in the public group
        ->assertSee('Posters');            // a public download
});

test('roze page sets the welcome-window cookie and greets the newcomer in the header', function () {
    $group = rozeChapter();
    $member = User::factory()->create();
    $group->users()->attach($member);

    // The old welcome *panel* is gone; the welcome-window cookie still rides along
    // because it drives the sub-nav (Aan de slag floats up for new members), and the
    // welcome moment now lives in the greeting header instead.
    actingAs($member)
        ->get(route('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertDontSee('Welkom bij de roze hesjes')
        ->assertSee('data-moment="welcome"', escape: false)
        ->assertSee('Fijn dat je meerijdt')
        ->assertCookie('roze_welcome_'.$group->id);
});

test('aan de slag shows the onboarding info', function () {
    $group = rozeChapter();
    $member = User::factory()->create();
    $group->users()->attach($member);

    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(route('groups.roze-hesjes.aan-de-slag', $group))
        ->assertOk()
        ->assertSee('Voor je eerste rit')
        ->assertSee('kindertempo');
});

test('the nav shows a roze chapter button for a logged-in member', function () {
    $group = rozeChapter();
    $member = User::factory()->create();
    $group->users()->attach($member);

    actingAs($member)
        ->get(route('groups.show', $group))
        ->assertOk()
        ->assertSee(route('groups.roze-hesjes', $group)); // a link to this member's roze page
});

test('the nav has no roze chapter button for guests', function () {
    $group = rozeChapter();

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee(route('groups.roze-hesjes', $group));
});
