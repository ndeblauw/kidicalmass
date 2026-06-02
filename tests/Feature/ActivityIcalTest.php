<?php

use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exports an ics file with a computed end time when duration is set', function () {
    $activity = Activity::factory()->create([
        'begin_date' => now()->setTime(14, 0),
        'duration_minutes' => 90,
    ]);

    $response = $this->get(route('activities.ical', $activity));

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/calendar; charset=utf-8');

    $end = $activity->begin_date->copy()->addMinutes(90)->utc()->format('Ymd\THis\Z');
    expect($response->getContent())
        ->toContain('BEGIN:VCALENDAR')
        ->toContain("DTEND:{$end}");
});

it('exports an ics file without crashing when duration is null', function () {
    $activity = Activity::factory()->create([
        'duration_minutes' => null,
    ]);

    $start = $activity->begin_date->utc()->format('Ymd\THis\Z');

    $response = $this->get(route('activities.ical', $activity));

    $response->assertOk();
    expect($response->getContent())
        ->toContain("DTSTART:{$start}")
        ->toContain("DTEND:{$start}");
});
