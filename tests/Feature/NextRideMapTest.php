<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function chapterWithNextRide(bool $withGpx): Group
{
    $group = Group::factory()->create();

    $factory = Activity::factory();
    if ($withGpx) {
        $factory = $factory->withFakeGpx();
    }

    $ride = $factory->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addWeek(),
    ]);
    $ride->groups()->attach($group->id);

    return $group;
}

it('renders the real route map in the next-ride card when the ride has a GPX track', function () {
    $group = chapterWithNextRide(withGpx: true);

    $this->get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('js-route-map', false)
        ->assertSee('data-interactive="false"', false)
        ->assertDontSee('next-ride__map-svg', false);
});

it('falls back to the faux placeholder when the next ride has no GPX', function () {
    $group = chapterWithNextRide(withGpx: false);

    $this->get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('next-ride__map-svg', false)
        ->assertDontSee('js-route-map', false);
});
