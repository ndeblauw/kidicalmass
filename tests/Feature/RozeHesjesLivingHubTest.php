<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;

/** A past, published ride attached to the group, dressed with $photos gallery images. */
function rozePastRideWithPhotos(Group $group, User $author, string $title, int $photos): Activity
{
    $ride = Activity::create([
        'title_nl' => $title,
        'title_fr' => $title,
        'content_nl' => 'Voorbij.',
        'content_fr' => 'Passé.',
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->subWeeks(2),
        'location' => 'Mortsel',
        'author_id' => $author->id,
        'published' => true,
    ]);
    $ride->groups()->attach($group->id);

    for ($i = 0; $i < $photos; $i++) {
        $ride->addMedia(UploadedFile::fake()->image("ride-{$ride->id}-{$i}.jpg", 800, 600))
            ->toMediaCollection('gallery');
    }

    return $ride;
}

/**
 * Living-hub iteration tests for the roze-hesje page.
 * Covers section ordering and the living-content slots reserved in Task 1.
 * See: docs/wiki/design/30-skeleton/chapters-roze-hesjes.md
 */
function rozeChapterWithMember(): array
{
    $group = Group::create([
        'shortname' => 'mortsel',
        'name' => 'Kidical Mass Mortsel',
        'zip' => '2640',
        'invisible' => false,
        'started_at' => now(),
    ]);
    $member = User::factory()->create(['name' => 'Saar Vermeulen']);
    $group->users()->attach($member);

    return [$group, $member];
}

test('roster marks a member who joined within the window as new', function () {
    [$group, $member] = rozeChapterWithMember();              // Saar attached now → recent
    $old = User::factory()->create(['name' => 'Wim Oud']);
    $group->users()->attach($old);
    $group->users()->updateExistingPivot($old->id, ['created_at' => now()->subWeeks(4)]);

    // Suppress the time-boxed welcome block so its "Nieuw hier?" copy cannot satisfy the assertion.
    $html = actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(route('groups.roze-hesjes.groep', $group))
        ->assertOk()
        ->getContent();

    // Scope each assertion to the member's own roster <li> so the badge is verified in place.
    $saarRow = Str::before(Str::after($html, 'Saar Vermeulen'), '</li>');
    $wimRow = Str::before(Str::after($html, 'Wim Oud'), '</li>');
    expect($saarRow)->toContain('Nieuw');      // recent member badged in its row
    expect($wimRow)->not->toContain('Nieuw');  // old member not
});

test('roster shows the real pivot role label', function () {
    [$group, $member] = rozeChapterWithMember();
    $captain = User::factory()->create(['name' => 'Katrien Peeters']);
    $group->users()->attach($captain, ['role' => 'captain']);

    $html = actingAs($member)
        ->get(route('groups.roze-hesjes.groep', $group))
        ->assertOk()
        ->getContent();

    // Scope to each member's roster <li>: the captain row says "Kapitein", a pinkvest row does not.
    $captainRow = Str::before(Str::after($html, 'Katrien Peeters'), '</li>');
    $memberRow = Str::before(Str::after($html, 'Saar Vermeulen'), '</li>');
    expect($captainRow)->toContain('Kapitein');
    expect($memberRow)->toContain('Roze hesje')->not->toContain('Kapitein');
});

test('roze hub feed surfaces the newest member, derived from real data', function () {
    [$group, $member] = rozeChapterWithMember(); // Saar attached now → within the welcome window

    actingAs($member)
        ->get(route('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertSee('Sinds je laatste bezoek')
        ->assertSee('Saar Vermeulen rijdt nu mee als roze hesje'); // dynamic new-member card
});

test('roze hub feed hides when nothing has changed', function () {
    [$group, $member] = rozeChapterWithMember();
    // Push the only member out of the welcome window so no feed event remains.
    $group->users()->updateExistingPivot($member->id, ['created_at' => now()->subWeeks(4)]);

    actingAs($member)
        ->get(route('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertDontSee('Sinds je laatste bezoek');
});

test('agenda leads with a real in-voorbereiding draft linking to its preview', function () {
    [$group, $member] = rozeChapterWithMember();

    $draft = Activity::create([
        'title_nl' => 'Testrit in voorbereiding',
        'title_fr' => 'Sortie test en préparation',
        'content_nl' => 'Nog in de maak.',
        'content_fr' => 'Encore en préparation.',
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addWeeks(2),
        'location' => 'Mortsel',
        'author_id' => $member->id,
        'published' => false, // a draft never reaches the public agenda
    ]);
    $draft->groups()->attach($group->id);

    actingAs($member)
        ->get(route('groups.roze-hesjes.agenda', $group))
        ->assertOk()
        ->assertSee('In voorbereiding')
        ->assertSee('Testrit in voorbereiding')
        ->assertSee(route('groups.ride-preview', [$group, 'ride' => $draft->id]), false);
});

test('fotos lists an album per past ride with a picker, newest by default', function () {
    Storage::fake('media');
    [$group, $member] = rozeChapterWithMember();

    rozePastRideWithPhotos($group, $member, 'Rit van april', 2);
    rozePastRideWithPhotos($group, $member, 'Rit van juni', 3);

    actingAs($member)
        ->get(route('groups.roze-hesjes.fotos', $group))
        ->assertOk()
        ->assertSee('Het gedeelde album van') // head, apostrophe-free fragment
        ->assertSee('Kies een rit')           // the ride picker (two albums)
        ->assertSee('ride-gallery__grid', false) // the real photo wall rendered
        ->assertDontSee('Nog geen');          // not the empty state
});

test('fotos shows a friendly empty state when no ride has photos', function () {
    [$group, $member] = rozeChapterWithMember();

    actingAs($member)
        ->get(route('groups.roze-hesjes.fotos', $group))
        ->assertOk()
        ->assertSee('Nog geen'); // empty-state copy, apostrophe-free fragment
});

test('aan de slag shows a whatsapp doorgang', function () {
    [$group, $member] = rozeChapterWithMember();

    actingAs($member)
        ->get(route('groups.roze-hesjes.aan-de-slag', $group))
        ->assertOk()
        ->assertSee('WhatsApp');
});

test('ride preview is membership-gated and shows one status line, marked not-yet-final', function () {
    [$group, $member] = rozeChapterWithMember();
    $outsider = User::factory()->create();

    actingAs($member)
        ->get(route('groups.ride-preview', $group))
        ->assertOk()
        ->assertSee('Nog niet vast')             // draftness is explicit
        ->assertSee('Wat moet er nog gebeuren')  // the single status line
        ->assertSee('de communicatiekaart');     // faux next-step content

    actingAs($outsider)
        ->get(route('groups.ride-preview', $group))
        ->assertForbidden();                     // read-only, but still hesje-only
});
