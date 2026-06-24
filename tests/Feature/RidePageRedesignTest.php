<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;

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
