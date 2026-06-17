<?php

use App\Models\Activity;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    app()->setLocale('nl');
    URL::defaults(['locale' => 'nl']); // ride-row links call route('activities.show', …)
});

it('renders a day lockup with a date rail and no distance text', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Etterbeek',
        'begin_date' => '2026-06-14 14:00',
    ]);

    $html = Blade::render(
        '<x-ride-day :period-key="$key" :rows="$rows" />',
        ['key' => '2026-06-14', 'rows' => [['item' => $ride]]],
    );

    expect($html)->toContain('ride-day__rail')
        ->toContain('14')        // day number
        ->toContain('juni')      // month
        ->toContain('Etterbeek')
        ->not->toContain('km van jou');
});

it('renders a month header above a day lockup per ride day', function () {
    $first = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Gent',
        'begin_date' => '2026-06-14 14:00',
    ]);
    $second = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Brugge',
        'begin_date' => '2026-06-21 14:00',
    ]);

    $html = Blade::render(
        '<x-ride-month :period-key="$key" :rides="$rides" />',
        ['key' => '2026-06', 'rides' => collect([$first, $second])],
    );

    // Month title once, then a day lockup (<x-ride-day>) for each distinct ride day.
    expect($html)->toContain('Juni 2026')
        ->toContain('Gent')
        ->toContain('Brugge')
        ->toContain('ride-month__days')
        ->and(substr_count($html, 'ride-day__rail'))->toBe(2);
});
