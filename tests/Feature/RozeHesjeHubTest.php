<?php

use App\Models\Group;
use App\Models\User;

use function Pest\Laravel\actingAs;

/** @return array<int, string> the 6 hub route names */
function hubRoutes(): array
{
    return [
        'groups.roze-hesjes',
        'groups.roze-hesjes.aan-de-slag',
        'groups.roze-hesjes.agenda',
        'groups.roze-hesjes.fotos',
        'groups.roze-hesjes.groep',
        'groups.roze-hesjes.materiaal',
    ];
}

function hubUrl(string $name, Group $group): string
{
    return route($name, ['locale' => 'nl', 'group' => $group]);
}

test('a member can open every hub page', function (string $name) {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl($name, $group))->assertOk();
})->with(hubRoutes());

test('a logged-in non-member is forbidden from every hub page', function (string $name) {
    $group = Group::factory()->create();

    actingAs(User::factory()->create())->get(hubUrl($name, $group))->assertForbidden();
})->with(hubRoutes());

test('the Beheer link shows for a captain and not for a plain member', function () {
    $group = Group::factory()->create();
    $captain = User::factory()->create();
    $plain = User::factory()->create();
    $group->users()->attach($captain, ['role' => 'captain']);
    $group->users()->attach($plain, ['role' => null]);

    actingAs($captain)->get(hubUrl('groups.roze-hesjes', $group))
        ->assertSee('roze-subnav__beheer', escape: false);
    actingAs($plain)->get(hubUrl('groups.roze-hesjes', $group))
        ->assertDontSee('roze-subnav__beheer', escape: false);
});

test('the Overview shows the Voor de rit tiles and the feed', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl('groups.roze-hesjes', $group))
        ->assertSee('Voor de rit')
        ->assertSee('Speech')
        ->assertSee('Playlist')
        ->assertSee('Sinds je laatste bezoek')
        ->assertSee("3 nieuwe foto's van de rit van zondag")
        ->assertSee('Sara rijdt nu mee als roze hesje');
});

test('the welcome panel shows on a first visit and hides afterwards', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    // First visit: no cookie yet -> welcome shown.
    actingAs($member)->get(hubUrl('groups.roze-hesjes', $group))
        ->assertSee('Welkom bij de roze hesjes');

    // Past the window: cookie dated long ago -> hidden.
    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(4)->toIso8601String())
        ->get(hubUrl('groups.roze-hesjes', $group))
        ->assertDontSee('Welkom bij de roze hesjes');
});

test('the De Groep sub-page lists a roster member by name', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create(['name' => 'Pieter Janssens']);
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl('groups.roze-hesjes.groep', $group))
        ->assertSee('Pieter Janssens');
});

test('each sub-page marks its own tab active', function (string $name, string $label) {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    $html = actingAs($member)->get(hubUrl($name, $group))->getContent();

    // The active tab carries the modifier and aria-current; assert the active label sits on it.
    expect($html)->toContain('aria-current="page"');
    expect($html)->toContain($label);
})->with([
    ['groups.roze-hesjes.aan-de-slag', 'Aan de slag'],
    ['groups.roze-hesjes.agenda', 'Agenda'],
    ['groups.roze-hesjes.fotos', 'Foto&#039;s'],
    ['groups.roze-hesjes.groep', 'De Groep'],
    ['groups.roze-hesjes.materiaal', 'Materiaal'],
]);

test('the hub renders the app-shell bar and hides the marketing nav', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek', 'invisible' => false]);
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertSee('roze-shell-bar', escape: false)
        ->assertSee('Schaarbeek', escape: false)
        ->assertDontSee('site-nav__links', escape: false)
        ->assertDontSee('steun-nav-btn', escape: false);
});

test('a member of one chapter sees a plain context label, no switcher', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek', 'invisible' => false]);
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl('groups.roze-hesjes', $group))
        ->assertSee('roze-shell-bar__context', escape: false)
        ->assertDontSee('roze-shell-switch', escape: false);
});

test('a member of multiple chapters gets a chapter switcher', function () {
    $here = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek', 'invisible' => false]);
    $other = Group::factory()->create(['name' => 'Kidical Mass Gent', 'invisible' => false]);
    $member = User::factory()->create();
    $here->users()->attach($member, ['role' => null]);
    $other->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl('groups.roze-hesjes', $here))
        ->assertSee('roze-shell-switch', escape: false)
        ->assertSee('Gent', escape: false);
});

test('the shell bar context label appears on every hub sub-page', function (string $name) {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl($name, $group))
        ->assertOk()
        ->assertSee('roze-shell-bar', escape: false);
})->with([
    'groups.roze-hesjes',
    'groups.roze-hesjes.agenda',
    'groups.roze-hesjes.groep',
    'groups.roze-hesjes.materiaal',
]);
