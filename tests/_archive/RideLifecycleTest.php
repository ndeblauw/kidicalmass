<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;

function showRide(Activity $ride)
{
    return test()->get(route('activities.show', ['locale' => 'nl', 'activity' => $ride]));
}

it('upcoming ride shows promises and the how-it-works CTA', function () {
    $ride = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addWeek(),
    ]);

    showRide($ride)
        ->assertSee('Wat kan je verwachten')
        // The how-it-works ask lives in the promises block above the map.
        ->assertSee('Zo werkt een rit')
        // The closing band no longer duplicates it: it nudges towards future rides.
        ->assertSee('Geen rit missen?')
        ->assertSee('Schrijf je in voor updates')
        ->assertSee(route('newsletter.show', ['locale' => 'nl']), escape: false)
        ->assertDontSee('Net gereden')
        // The support ask only appears once the ride is in the past.
        ->assertDontSee('Steun de volgende rit');
});

it('upcoming ride closing band names the organising group', function () {
    $group = Group::factory()->create(['name' => 'Etterbeek', 'zip' => '1040']);
    $ride = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addWeek(),
    ]);
    $ride->groups()->attach($group);

    showRide($ride)
        ->assertSee('Mis geen rit van Kidical Mass Etterbeek')
        ->assertDontSee('Geen rit missen?');
});

it('just-past ride shows the photo nudge, drops promises, points to the chapter', function () {
    $group = Group::factory()->create();
    $ride = Activity::factory()->past()->create();
    $ride->groups()->attach($group);

    showRide($ride)
        ->assertSee('Net gereden')
        ->assertDontSee('Wat kan je verwachten')
        ->assertDontSee('Lees hoe je meerijdt')
        // Past ride: the support ask uses its past-tense hook.
        ->assertSee('Fijn meegereden')
        ->assertSee(route('groups.show', ['locale' => 'nl', 'group' => $group]), escape: false);
});

it('recap ride shows the gallery, drops promises, points to the chapter', function () {
    $group = Group::factory()->create();
    $ride = Activity::factory()->past()->withGallery(3)->create();
    $ride->groups()->attach($group);

    showRide($ride)
        ->assertSee('ride-gallery__grid', escape: false)
        ->assertDontSee('Wat kan je verwachten')
        ->assertDontSee('Net gereden')
        ->assertDontSee('Lees hoe je meerijdt')
        ->assertSee('Fijn meegereden')
        ->assertSee(route('groups.show', ['locale' => 'nl', 'group' => $group]), escape: false)
        // 3 photos fit on the wall, so no "more" overlay.
        ->assertDontSee('ride-gallery__more', escape: false);
});

it('recap gallery surfaces a "view all photos" overlay when the wall is capped', function () {
    $ride = Activity::factory()->past()->withGallery(8)->create();
    $ride->groups()->attach(Group::factory()->create());

    // 8 photos: 1 cover + 5 tiles shown, 2 hidden behind the last tile's overlay.
    showRide($ride)
        ->assertSee('ride-gallery__more', escape: false)
        ->assertSee('+2')
        ->assertSee("Bekijk alle foto's");
});
