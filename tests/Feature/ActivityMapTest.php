<?php

use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the komoot link when komoot_url is set on the activity', function () {
    $activity = Activity::factory()->withFakeGpx()->create([
        'komoot_url' => 'https://www.komoot.com/tour/1234567',
    ]);

    $this->get(route('activities.show', $activity))
        ->assertOk()
        ->assertSee('Bekijk op Komoot')
        ->assertSee('https://www.komoot.com/tour/1234567');
});

it('does not show the komoot link when komoot_url is not set', function () {
    $activity = Activity::factory()->create([
        'komoot_url' => null,
    ]);

    $this->get(route('activities.show', $activity))
        ->assertOk()
        ->assertDontSee('Bekijk op Komoot');
});

it('renders the share handler without leaking an uncompiled @js directive', function () {
    $activity = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Gent',
    ]);

    $this->get(route('activities.show', $activity))
        ->assertOk()
        ->assertDontSee('@js(', escape: false)
        ->assertSee("shareTitle: 'Kidical Mass Gent'", escape: false);
});
