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
        ->assertSee('Wat kun je verwachten')
        ->assertSee('Lees hoe je meerijdt')
        ->assertDontSee('Net gereden');
});

it('just-past ride shows the photo nudge, drops promises, points to the chapter', function () {
    $group = Group::factory()->create();
    $ride = Activity::factory()->past()->create();
    $ride->groups()->attach($group);

    showRide($ride)
        ->assertSee('Net gereden')
        ->assertDontSee('Wat kun je verwachten')
        ->assertDontSee('Lees hoe je meerijdt')
        ->assertSee(route('groups.show', ['locale' => 'nl', 'group' => $group]), escape: false);
});

it('recap ride shows the gallery, drops promises, points to the chapter', function () {
    $group = Group::factory()->create();
    $ride = Activity::factory()->past()->withGallery(3)->create();
    $ride->groups()->attach($group);

    showRide($ride)
        ->assertSee('ride-gallery__grid', escape: false)
        ->assertDontSee('Wat kun je verwachten')
        ->assertDontSee('Net gereden')
        ->assertDontSee('Lees hoe je meerijdt')
        ->assertSee(route('groups.show', ['locale' => 'nl', 'group' => $group]), escape: false);
});
