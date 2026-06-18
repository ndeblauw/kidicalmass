<?php

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;

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

test('roze hub shows a wat-is-nieuw strook', function () {
    [$group, $member] = rozeChapterWithMember();

    actingAs($member)
        ->get(route('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertSee('Sinds je laatste bezoek')
        ->assertSee('Sara rijdt nu mee als roze hesje'); // faux feed item on the Overview
});

test('agenda shows an in-voorbereiding draft block linking to a preview', function () {
    [$group, $member] = rozeChapterWithMember();

    actingAs($member)
        ->get(route('groups.roze-hesjes.agenda', $group))
        ->assertOk()
        ->assertSee('In voorbereiding')
        ->assertSee(route('groups.ride-preview', $group), false);
});

test('fotos shows a gallery slot', function () {
    [$group, $member] = rozeChapterWithMember();

    // Assert on an apostrophe-free fragment: the literal "'" in the template stays as "'",
    // but assertSee()'s default escaping would look for "&#039;" and miss it.
    actingAs($member)
        ->get(route('groups.roze-hesjes.fotos', $group))
        ->assertOk()
        ->assertSee('van het chapter');
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
