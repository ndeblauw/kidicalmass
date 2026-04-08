<?php

use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the komoot link when komoot_url is set on the activity', function () {
    $activity = Activity::factory()->create([
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
