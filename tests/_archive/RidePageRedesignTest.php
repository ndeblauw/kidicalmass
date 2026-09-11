<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Blade;

use function Pest\Laravel\get;

function makeRide(array $attributes = []): Activity
{
    return Activity::factory()->create(array_merge([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Etterbeek',
        'content_nl' => 'Een vrolijke gezinsrit door autovrije straten.',
        'begin_date' => now()->addDays(5)->setTime(14, 0),
        'location' => 'Jubelpark, Brussel',
        'distance' => '6 km',
    ], $attributes));
}

function rideUrl(Activity $activity): string
{
    return route('activities.show', ['locale' => 'nl', 'activity' => $activity]);
}

it('renders the shared share-links controls with all channels', function () {
    $html = Blade::render(
        '<x-share-links url="https://example.test/rit" title="Kidical Mass" date="zondag 28 juni" />'
    );

    expect($html)
        ->toContain('share-band__channels')
        ->toContain('wa.me')                 // WhatsApp
        ->toContain('facebook.com/sharer')   // Facebook
        ->toContain('mailto:')               // e-mail
        ->toContain('Kopieer link');         // copy button
});

it('eager-loads the organising group so its name renders (not masked by the activity title)', function () {
    $group = Group::factory()->create(['name' => 'Fietsersbond Etterbeek', 'zip' => '1040']);
    $member = User::factory()->create(['name' => 'Marieke Janssens']);
    $group->users()->attach($member, ['role' => 'trekker', 'is_public' => true]);

    $ride = makeRide();
    $ride->groups()->attach($group);

    get(rideUrl($ride))
        ->assertOk()
        ->assertSee('Fietsersbond Etterbeek');
});

it('anchors the hero with the large date tile and the description as hero lead', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Etterbeek', 'zip' => '1040']);
    $ride = makeRide([
        'content_nl' => 'Een vrolijke gezinsrit door autovrije straten.',
        'begin_date' => now()->setDate(2026, 6, 28)->setTime(14, 0),
    ]);
    $ride->groups()->attach($group);

    get(rideUrl($ride))
        ->assertOk()
        ->assertSee('activity-head__date', false)             // large date tear-off anchors the headline
        ->assertSee('ride-day__cal--lg', false)               // ...in its large variant
        ->assertSee('Een vrolijke gezinsrit')                 // description rendered as hero lead
        ->assertSee('1040')                                   // group zip rides beside the nav logo
        ->assertSee('site-nav__postcode', false)              // ...via the chapter postcode lockup
        ->assertDontSee('activity-head__eyebrow', false)      // no date·time eyebrow (time lives in Praktisch)
        ->assertDontSee('activity-head__share', false)        // share links no longer in the hero
        ->assertDontSee('activity-head__org', false)          // group lockup no longer repeated in the hero
        ->assertDontSee('activity-head__chapter', false);     // old pin lockup gone
});

it('shows the full date under Wanneer, an always-on route, and a share panel beside the facts', function () {
    // Freeze time so the ride below is reliably upcoming (the share panel shows the
    // "Vrienden mee?" invite for future rides and the recap copy for past ones).
    $this->travelTo(now()->setDate(2026, 6, 20)->setTime(9, 0));

    $group = Group::factory()->create(['name' => 'Kidical Mass Etterbeek', 'zip' => '1040']);
    $ride = makeRide([
        'begin_date' => now()->setDate(2026, 6, 28)->setTime(14, 0),
    ]); // no GPX media → faux route fallback must still render
    $ride->groups()->attach($group);

    get(rideUrl($ride))
        ->assertOk()
        ->assertSee('activity-praktisch', false)        // the two-column wrapper
        ->assertSee('activity-facts__route-faux', false) // route shown even without a GPX file
        ->assertSee('Wanneer')                           // date+time fact (renamed from Startuur)
        ->assertSee('juni')                              // full date (not just the time) under Wanneer
        ->assertSee('activity-share', false)             // the in-context share panel beside the facts
        ->assertSee('Vrienden mee?');                    // upcoming-ride share copy
});

it('shows a compact real-volunteer row and no pink-vest recruitment CTA', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Etterbeek', 'zip' => '1040']);
    $member = User::factory()->create(['name' => 'Marieke Janssens']);
    $group->users()->attach($member, ['role' => 'trekker', 'is_public' => true]);

    $ride = makeRide();
    $ride->groups()->attach($group);

    get(rideUrl($ride))
        ->assertOk()
        ->assertSee('Dankzij buren zoals jij.')        // the new self-organising-crew heading
        ->assertSee('activity-team__stack', false)     // the social-proof face-stack
        ->assertSee('Marieke')                         // real member, first name only
        ->assertSee('deze ritten mogelijk')            // the credit line
        ->assertSee('Leer Kidical Mass Etterbeek kennen') // CTA names the local group
        ->assertDontSee('Janssens')                    // surname dropped
        ->assertDontSee('Roze hesje worden?')          // pink-vest CTA removed
        ->assertDontSee('activity-volunteer', false)   // recruitment block gone
        ->assertDontSee('volunteer-signup', false);    // inline livewire reveal gone
});

it('hides the team section when the organising group has no members', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Leeg', 'zip' => '9000']);
    $ride = makeRide();
    $ride->groups()->attach($group);

    get(rideUrl($ride))
        ->assertOk()
        ->assertDontSee('activity-team__stack', false);
});

it('excludes group members who opted out of the public roster (is_public = false)', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Etterbeek', 'zip' => '1040']);
    $group->users()->attach(User::factory()->create(['name' => 'Marieke Janssens']), ['is_public' => true]);
    $group->users()->attach(User::factory()->create(['name' => 'Bram Verhaeghe']), ['is_public' => false]);

    $ride = makeRide();
    $ride->groups()->attach($group);

    get(rideUrl($ride))
        ->assertOk()
        ->assertSee('Marieke')      // public member shown
        ->assertDontSee('Bram');    // opted-out member hidden
});
