<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    app()->setLocale('nl');
    URL::defaults(['locale' => 'nl']); // route('activities.show', …) needs the {locale} param
});

it('renders a normal ride without a type chip', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Etterbeek',
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => '2026-06-14 14:00',
        'location' => 'Jubelpark',
    ]);

    $html = Blade::render('<x-ride-row :activity="$activity" />', ['activity' => $ride]);

    expect($html)->toContain('Etterbeek')
        ->toContain('14u')
        ->toContain('Jubelpark')
        ->not->toContain('ride-row__chip');
});

it('shows a yellow chip for a workshop', function () {
    $workshop = Activity::factory()->create([
        'title_nl' => 'Fietsherstel',
        'activity_type' => ActivityType::WORKSHOP,
        'begin_date' => '2026-06-14 19:00',
    ]);

    $html = Blade::render('<x-ride-row :activity="$activity" />', ['activity' => $workshop]);

    expect($html)->toContain('ride-row__chip--workshop')->toContain('Workshop');
});

it('shows the inline date only when showDate is set', function () {
    $ride = Activity::factory()->create(['begin_date' => '2026-06-14 14:00']);

    $withDate = Blade::render('<x-ride-row :activity="$activity" :show-date="true" />', ['activity' => $ride]);
    $without = Blade::render('<x-ride-row :activity="$activity" />', ['activity' => $ride]);

    expect($withDate)->toContain('ride-row__date');
    expect($without)->not->toContain('ride-row__date');
});

it('marks a flagship ride as featured', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Grote Kidical Mass Gent',
        'begin_date' => '2026-06-14 14:00',
    ]);

    $html = Blade::render('<x-ride-row :activity="$activity" />', ['activity' => $ride]);

    expect($html)->toContain('ride-row--featured')->toContain('ride-row__star')->toContain('Uitgelicht');
});

it('strips a trailing commune from the venue when it duplicates the headline', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Etterbeek',
        'begin_date' => '2026-06-14 14:00',
        'location' => 'Jubelpark, Etterbeek',
    ]);

    $html = Blade::render('<x-ride-row :activity="$activity" />', ['activity' => $ride]);

    expect($html)->toContain('Jubelpark')->not->toContain('Jubelpark, Etterbeek');
});
