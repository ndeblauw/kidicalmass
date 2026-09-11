<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\PostalCode;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('renders the lighter basic page for a workshop, not the ride layout', function () {
    $activity = Activity::factory()->create([
        'activity_type' => ActivityType::WORKSHOP,
        'title_nl' => 'Fietscheck & sleutelworkshop',
        'content_nl' => 'Breng je fiets langs voor een gratis veiligheidscheck.',
        'komoot_url' => 'https://www.komoot.com/tour/1234567',
    ]);

    get(route('activities.show', ['locale' => 'nl', 'activity' => $activity]))
        ->assertOk()
        ->assertSee('data-activity-layout="basic"', escape: false)
        ->assertSee('Workshop')
        ->assertSee('Breng je fiets langs voor een gratis veiligheidscheck.')
        // None of the ride-only spine leaks onto a workshop.
        ->assertDontSee('Op het tempo van het jongste kind')
        ->assertDontSee('Bekijk op Komoot')
        ->assertDontSee('Lees hoe je meerijdt');
});

it('pins the venue on a location-only map when the postal code is known', function () {
    PostalCode::create(['zip' => '1030', 'name' => 'Schaerbeek', 'latitude' => 50.8694, 'longitude' => 4.3774]);

    $meeting = Activity::factory()->create([
        'activity_type' => ActivityType::MEETING,
        'location' => 'Maison des Citoyens, Schaarbeek',
        'postal_code' => '1030',
    ]);

    get(route('activities.show', ['locale' => 'nl', 'activity' => $meeting]))
        ->assertOk()
        // The map gets a single coordinate (pin only, no route to draw)...
        ->assertSee('js-route-map', escape: false)
        ->assertSee('data-coordinates="[[50.8694,4.3774]]"', escape: false)
        // ...labelled with the venue, with the accessible fallback label present.
        ->assertSee('Locatie')
        ->assertSee('Maison des Citoyens, Schaarbeek');
});

it('falls back to a "Waar" fact row when no coordinate is known for the venue', function () {
    $workshop = Activity::factory()->create([
        'activity_type' => ActivityType::WORKSHOP,
        'location' => 'Cyclo werkplaats, Anderlecht',
        'postal_code' => '1070', // no postal_codes row seeded → no pin
    ]);

    get(route('activities.show', ['locale' => 'nl', 'activity' => $workshop]))
        ->assertOk()
        ->assertDontSee('js-route-map')
        ->assertSee('Waar')
        ->assertSee('Cyclo werkplaats, Anderlecht');
});

it('shows the external "Meer info" link only when a commute_link is set', function () {
    $withLink = Activity::factory()->create([
        'activity_type' => ActivityType::WORKSHOP,
        'commute_link' => 'https://facebook.com/events/123',
    ]);
    $withoutLink = Activity::factory()->create([
        'activity_type' => ActivityType::WORKSHOP,
        'commute_link' => null,
    ]);

    get(route('activities.show', ['locale' => 'nl', 'activity' => $withLink]))
        ->assertOk()
        ->assertSee('Meer info')
        ->assertSee('https://facebook.com/events/123', escape: false);

    get(route('activities.show', ['locale' => 'nl', 'activity' => $withoutLink]))
        ->assertOk()
        ->assertDontSee('Meer info');
});

it('keeps a volunteer meeting quiet: no broadcast share band', function () {
    $meeting = Activity::factory()->create([
        'activity_type' => ActivityType::MEETING,
        'commute_link' => 'https://facebook.com/events/456',
    ]);

    get(route('activities.show', ['locale' => 'nl', 'activity' => $meeting]))
        ->assertOk()
        ->assertSee('Meer info voor vrijwilligers')
        ->assertDontSee('Vrienden mee?');
});

it('links each "Ook in {gemeente}" card to the activity detail page', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek']);
    $workshop = Activity::factory()->create([
        'activity_type' => ActivityType::WORKSHOP,
        'title_nl' => 'Fietscheck & sleutelworkshop Schaarbeek',
        'location' => 'Cyclo werkplaats, Schaarbeek',
        'begin_date' => now()->addWeek(),
    ]);
    $workshop->groups()->attach($group);

    // The whole <x-other-activity> row links to the detail page; the commune is dropped
    // from both the title and the venue (redundant on the chapter's own page).
    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Fietscheck & sleutelworkshop')
        ->assertDontSee('Fietscheck & sleutelworkshop Schaarbeek')
        ->assertDontSee('Cyclo werkplaats, Schaarbeek')
        ->assertSee(route('activities.show', $workshop), escape: false);
});
