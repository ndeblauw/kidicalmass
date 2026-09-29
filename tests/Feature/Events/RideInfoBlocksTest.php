<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Support\RideText;

use function Pest\Laravel\get;

beforeEach(function (): void {
    $this->withoutVite();
});

dataset('ride page locales', [
    'nl' => ['nl', '/nl/events/'],
    'fr' => ['fr', '/fr/agenda/'],
]);

it('shows the good-to-know block on an upcoming ride, with the photo consent folded in', function (string $locale, string $prefix) {
    $ride = Activity::factory()->create(['activity_type' => ActivityType::KIDICALMASS]);
    $items = RideText::goodToKnowItems(__('activities.good_to_know.items', [], $locale));

    expect($items)->toHaveCount(count(RideText::goodToKnowItems(__('activities.good_to_know.items', [], $locale === 'nl' ? 'fr' : 'nl'))));

    get($prefix.$ride->getRouteKey())
        ->assertOk()
        ->assertSee('data-ride-good-to-know', escape: false)
        ->assertSee(__('activities.good_to_know.heading', [], $locale))
        ->assertSeeInOrder(array_column($items, 'text'))
        ->assertDontSee('activity-permission', escape: false);
})->with('ride page locales');

it('leaves the good-to-know block off past rides and workshops', function () {
    $pastRide = Activity::factory()->past()->create();
    $workshop = Activity::factory()->create(['activity_type' => ActivityType::WORKSHOP]);

    get('/nl/events/'.$pastRide->getRouteKey())
        ->assertOk()
        ->assertDontSee('data-ride-good-to-know', escape: false);

    get('/nl/events/'.$workshop->getRouteKey())
        ->assertOk()
        ->assertSee('data-activity-layout="basic"', escape: false)
        ->assertDontSee('data-ride-good-to-know', escape: false);
});

it('shows the extra info note on an upcoming ride in the record language, sanitised', function (string $locale, string $prefix) {
    $group = Group::factory()->create(['name_nl' => 'Gent', 'name_fr' => 'Gand']);
    $notes = ['nl' => 'Onderweg stoppen we bij de bakker.', 'fr' => 'Pause goûter au parc.'];
    $ride = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'extra_info_nl' => $notes['nl']."\n\n<script>alert('nl')</script>",
        'extra_info_fr' => $notes['fr']."\n\n<script>alert('fr')</script>",
    ]);
    $ride->groups()->attach($group);

    $other = $locale === 'nl' ? 'fr' : 'nl';

    get($prefix.$ride->getRouteKey())
        ->assertOk()
        ->assertSee('data-activity-extra-info', escape: false)
        ->assertSee(__('activities.extra_info.from_group', ['name' => $group->{'name_'.$locale}], $locale))
        ->assertSee($notes[$locale])
        ->assertDontSee($notes[$other])
        ->assertDontSee('<script>alert', escape: false);
})->with('ride page locales');

it('renders no extra info note when the field is empty, and falls back to the organisers heading without a group', function () {
    $empty = Activity::factory()->create(['activity_type' => ActivityType::KIDICALMASS]);
    $groupless = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'extra_info_nl' => 'Start aan de achterkant van het station.',
    ]);

    get('/nl/events/'.$empty->getRouteKey())
        ->assertOk()
        ->assertDontSee('data-activity-extra-info', escape: false);

    get('/nl/events/'.$groupless->getRouteKey())
        ->assertOk()
        ->assertSee('data-activity-extra-info', escape: false)
        ->assertSee(__('activities.extra_info.from_organisers', [], 'nl'));
});

it('shows the extra info note on a workshop', function () {
    $workshop = Activity::factory()->create([
        'activity_type' => ActivityType::WORKSHOP,
        'extra_info_nl' => 'Breng je eigen bandenlichters mee.',
    ]);

    get('/nl/events/'.$workshop->getRouteKey())
        ->assertOk()
        ->assertSee('data-activity-layout="basic"', escape: false)
        ->assertSee('data-activity-extra-info', escape: false)
        ->assertSee('Breng je eigen bandenlichters mee.');
});
