<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the shared route map when the ride has a GPX track', function () {
    $activity = Activity::factory()->withFakeGpx()->create([
        'activity_type' => ActivityType::KIDICALMASS,
    ]);

    $this->get(route('activities.show', $activity))
        ->assertOk()
        ->assertSee('js-route-map', false)
        ->assertSee('unpkg.com/leaflet', false);
});

it('shows the komoot link when komoot_url is set on the activity', function () {
    $activity = Activity::factory()->withFakeGpx()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'komoot_url' => 'https://www.komoot.com/tour/1234567',
    ]);

    $this->get(route('activities.show', $activity))
        ->assertOk()
        ->assertSee('Bekijk op Komoot')
        ->assertSee('https://www.komoot.com/tour/1234567');
});

it('does not show the komoot link when komoot_url is not set', function () {
    $activity = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'komoot_url' => null,
    ]);

    $this->get(route('activities.show', $activity))
        ->assertOk()
        ->assertDontSee('Bekijk op Komoot');
});

it('renders the share band on the activity page', function () {
    $activity = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Gent',
    ]);

    $this->get(route('activities.show', $activity))
        ->assertOk()
        ->assertSee('Ken je een gezin dat dit leuk zou vinden?')
        ->assertSee('Kidical Mass Gent');
});
