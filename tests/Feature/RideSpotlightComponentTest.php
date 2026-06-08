<?php

use App\Models\Activity;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    app()->setLocale('nl');
    URL::defaults(['locale' => 'nl']); // CTA + links call route('activities.show', …)
});

it('renders the spotlight with a CTA when requested', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Gent',
        'begin_date' => '2026-06-14 14:00',
        'location' => 'Citadelpark',
    ]);

    $html = Blade::render('<x-ride-spotlight :activity="$activity" :cta="true" />', ['activity' => $ride]);

    expect($html)->toContain('Naar de rit')
        ->toContain('Zondag 14 juni')
        ->toContain('14u')
        ->toContain('Citadelpark');
});

it('omits the CTA by default and renders the requested heading level', function () {
    $ride = Activity::factory()->create(['title_nl' => 'Kidical Mass Gent', 'begin_date' => '2026-06-14 14:00']);

    $html = Blade::render('<x-ride-spotlight :activity="$activity" heading="h1" />', ['activity' => $ride]);

    expect($html)->toContain('<h1')->not->toContain('Naar de rit');
});

it('can render date-only (no time, no location) for the detail header', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Gent',
        'begin_date' => '2026-06-14 14:00',
        'location' => 'Citadelpark',
    ]);

    $html = Blade::render(
        '<x-ride-spotlight :activity="$activity" heading="h1" :show-time="false" :show-location="false" />',
        ['activity' => $ride],
    );

    expect($html)->toContain('Zondag 14 juni')
        ->not->toContain('14u')
        ->not->toContain('Verzamelen');
});

it('shows the daisy motif when there is no photo', function () {
    $ride = Activity::factory()->create(['title_nl' => 'Kidical Mass Gent', 'begin_date' => '2026-06-14 14:00']);

    // Partial-mock to force getFirstMedia('main') → null regardless of what the factory attached.
    $mock = Mockery::mock($ride)->makePartial();
    $mock->shouldReceive('getFirstMedia')->with('main')->andReturn(null);

    $html = Blade::render('<x-ride-spotlight :activity="$activity" />', ['activity' => $mock]);

    expect($html)->toContain('ride-spotlight__media--empty')->not->toContain('ride-spotlight__img');
});
