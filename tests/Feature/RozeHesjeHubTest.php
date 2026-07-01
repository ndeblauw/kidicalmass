<?php

use App\Models\Activity;
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
        // The member just joined, so the feed surfaces the dynamic new-member card.
        ->assertSee('rijdt nu mee als roze hesje');
});

test('the overview shows no welcome panel, even on a first visit', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    // The welcome banner was removed; a brand-new member (welcome window open,
    // no cookie yet) lands straight on the content with no panel.
    actingAs($member)->get(hubUrl('groups.roze-hesjes', $group))
        ->assertDontSee('roze-hub-welcome', escape: false)
        ->assertDontSee('Fijn dat je meerijdt');
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

test('hub pages show the slim member footer, not the public marketing footer', function (string $name) {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl($name, $group))
        ->assertSee('roze-foot', escape: false)          // slim member footer chrome
        ->assertSee('Terug naar de website')             // calm way back to the public site
        ->assertSee('Hulp nodig?')
        ->assertDontSee('site-footer__main', escape: false); // no carnival/Steun marketing footer
})->with(hubRoutes());

test('materiaal previews not-yet-available items as Binnenkort instead of dead links', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl('groups.roze-hesjes.materiaal', $group))
        ->assertSee('Binnenkort')                          // honest availability marker
        ->assertSee('roze-material--soon', escape: false)  // non-interactive preview styling
        ->assertSee('Voor de hesjes');                     // still previews the besloten group
});

test('the agenda renders drafts and confirmed rides through the one shared row', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    $confirmed = Activity::factory()->create([
        'is_published' => true,
        'title_nl' => 'Vastgelegde rit',
        'begin_date' => now()->addWeeks(2)->setTime(14, 0),
    ]);
    $confirmed->groups()->attach($group);

    $draft = Activity::factory()->create([
        'is_published' => false,
        'title_nl' => 'Rit in wording',
        'begin_date' => now()->addWeeks(3)->setTime(14, 0),
    ]);
    $draft->groups()->attach($group);

    actingAs($member)->get(hubUrl('groups.roze-hesjes.agenda', $group))
        ->assertOk()
        ->assertSee('Vastgelegde rit')
        ->assertSee('Rit in wording')
        ->assertSee('Nog niet vast')                         // the draft state chip
        ->assertSee('roze-agenda-row', escape: false)        // both lists share the one row
        ->assertSee('roze-agenda-row--draft', escape: false) // the draft is that row, softened
        // The draft links to the live preview; the confirmed ride to its public page.
        ->assertSee(route('groups.ride-preview', [$group, 'ride' => $draft->id]), escape: false)
        ->assertSee(route('activities.show', $confirmed), escape: false)
        // The bespoke draft card is retired.
        ->assertDontSee('roze-draft__flag', escape: false);
});

test('the aan-de-slag WhatsApp hand-off reads as binnenkort, not a button to nowhere', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl('groups.roze-hesjes.aan-de-slag', $group))
        ->assertSee('WhatsApp')
        ->assertSee('binnenkort')
        ->assertSee('roze-whatsapp__btn--soon', escape: false);
});
