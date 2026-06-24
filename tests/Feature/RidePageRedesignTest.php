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

it('shows the date·time eyebrow, the description as hero lead, and the group zip', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Etterbeek', 'zip' => '1040']);
    $ride = makeRide([
        'content_nl' => 'Een vrolijke gezinsrit door autovrije straten.',
        'begin_date' => now()->setDate(2026, 6, 28)->setTime(14, 0),
    ]);
    $ride->groups()->attach($group);

    get(rideUrl($ride))
        ->assertOk()
        ->assertSee('activity-head__eyebrow', false)          // yellow date·time eyebrow exists
        ->assertSee('14:00')                                  // time present in the eyebrow
        ->assertSee('Een vrolijke gezinsrit')                 // description rendered as hero lead
        ->assertSee('1040')                                   // group zip on the logo lockup
        ->assertSee('activity-head__share', false)            // share links at the bottom of the hero
        ->assertDontSee('activity-head__date', false)         // old date treatment gone
        ->assertDontSee('activity-head__chapter', false);     // old pin lockup gone
});

it('shows the full date in Startuur, an always-on route, and an updates card beside Praktisch', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Etterbeek', 'zip' => '1040']);
    $ride = makeRide([
        'begin_date' => now()->setDate(2026, 6, 28)->setTime(14, 0),
    ]); // no GPX media → faux route fallback must still render
    $ride->groups()->attach($group);

    get(rideUrl($ride))
        ->assertOk()
        ->assertSee('activity-praktisch', false)        // the two-column wrapper
        ->assertSee('activity-facts__route-faux', false) // route shown even without a GPX file
        ->assertSee('Startuur')
        ->assertSee('juni')                              // full date (not just the time) in Startuur
        ->assertSee('Mis geen rit');                     // <x-newsletter-optin> guest copy = the updates card
});
