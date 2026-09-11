<?php

use App\Enums\ActivityType;
use App\Models\Activity;

use function Pest\Laravel\get;

it('renders the date-rail lockup and rows on the events page, without legacy markup', function () {
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Etterbeek',
        'begin_date' => now()->addDays(5)->setTime(14, 0),
        'location' => 'Jubelpark',
    ]);

    get('/nl/events')
        ->assertOk()
        ->assertSee('ride-day__rail', false)
        ->assertSee('ride-row', false)
        ->assertSee('Etterbeek')
        ->assertDontSee('event-card', false)
        ->assertDontSee('km van jou');
});

it('renders the contained blue hero (h1, not the spotlight card) on an activity detail page', function () {
    $activity = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Gent',
        'begin_date' => now()->addDays(5)->setTime(14, 0),
        'location' => 'Citadelpark',
    ]);

    get(route('activities.show', ['locale' => 'nl', 'activity' => $activity]))
        ->assertOk()
        ->assertSee('activity-head__', false)
        ->assertSee('<h1', false)
        ->assertDontSee('ride-spotlight', false);
});
