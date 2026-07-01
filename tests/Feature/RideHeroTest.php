<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

function rideOrganisedBy(Group $group): Activity
{
    $activity = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass parade',
        'content_nl' => 'Een rustige fietsparade voor gezinnen.',
        'begin_date' => now()->addWeeks(2)->setTime(14, 0),
        'published' => true,
    ]);
    $activity->groups()->attach($group);

    return $activity;
}

it('anchors the ride hero with the shared date tear-off tile', function () {
    $activity = rideOrganisedBy(Group::factory()->create());

    get(route('activities.show', ['locale' => 'nl', 'activity' => $activity]))
        ->assertOk()
        // The hero reuses the agenda lockup via <x-ride-date-tile>, large. It drops the
        // weekday line and wears the friendlier hero face via the .activity-head__date scope.
        ->assertSee('activity-head__date', escape: false)
        ->assertSee('ride-day__cal--lg', escape: false)
        ->assertDontSee('ride-day__day', escape: false)
        // No date·time eyebrow: the tile carries the day, time lives in Praktisch.
        ->assertDontSee('activity-head__eyebrow', escape: false);
});

it('rolls the brand parade in along the foot of the ride hero', function () {
    $activity = rideOrganisedBy(Group::factory()->create());

    get(route('activities.show', ['locale' => 'nl', 'activity' => $activity]))
        ->assertOk()
        ->assertSee('activity-head__parade', escape: false)
        ->assertSee('activity-head__rider', escape: false)
        // The recognizable brand illustrations greet you up top, not just at the closing band.
        ->assertSee('illustrations/waving-rider.svg', escape: false);
});

it('shows the organising group postcode beside the nav logo on a ride page', function () {
    $group = Group::factory()->create(['zip' => '2018']);
    $activity = rideOrganisedBy($group);

    get(route('activities.show', ['locale' => 'nl', 'activity' => $activity]))
        ->assertOk()
        ->assertSee('site-nav__postcode', escape: false)
        ->assertSee('2018');
});

it('drops the repeated group lockup and share buttons from the hero', function () {
    $activity = rideOrganisedBy(Group::factory()->create(['name' => 'Kidical Mass Antwerpen Noord']));

    $response = get(route('activities.show', ['locale' => 'nl', 'activity' => $activity]))
        ->assertOk()
        ->assertDontSee('activity-head__org', escape: false)
        ->assertDontSee('activity-head__share', escape: false);

    // The group is still credited lower down in "Van en voor de buurt".
    $response->assertSee('Kidical Mass Antwerpen Noord');
});
