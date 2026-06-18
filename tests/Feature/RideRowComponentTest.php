<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    app()->setLocale('nl');
    URL::defaults(['locale' => 'nl']); // route('activities.show', …) needs the {locale} param
});

it('keeps the full ride title (commune) when there is no chapter context', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Etterbeek',
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => '2026-06-14 14:00',
        'location' => 'Jubelpark',
    ]);

    $html = Blade::render('<x-ride-row :activity="$activity" />', ['activity' => $ride]);

    expect($html)->toContain('Etterbeek')   // the commune still identifies the ride
        ->toContain('14u')
        ->toContain('Jubelpark')
        ->not->toContain('ride-row__type')   // the eyebrow label is gone everywhere
        ->not->toContain('Fietsparade');
});

it('turns a plain ride into "Fietsparade" inside its own chapter', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Etterbeek',
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => '2026-06-14 14:00',
    ]);

    $html = Blade::render('<x-ride-row :activity="$activity" :commune="$commune" />', [
        'activity' => $ride, 'commune' => 'Etterbeek',
    ]);

    // The page already says Etterbeek, so the ride drops to its essence.
    expect($html)->toContain('Fietsparade')
        ->not->toContain('Etterbeek');
});

it('drops the commune from a named activity inside its chapter, keeping the name', function () {
    $workshop = Activity::factory()->create([
        'title_nl' => 'Fietscheck en sleutelworkshop Etterbeek',
        'activity_type' => ActivityType::WORKSHOP,
        'begin_date' => '2026-06-14 19:00',
        'location' => 'Cyclo werkplaats, Etterbeek',
    ]);

    $html = Blade::render('<x-ride-row :activity="$activity" :commune="$commune" />', [
        'activity' => $workshop, 'commune' => 'Etterbeek',
    ]);

    expect($html)->toContain('Fietscheck en sleutelworkshop')
        ->toContain('Cyclo werkplaats')
        ->not->toContain('Etterbeek')   // dropped from both title and venue
        ->not->toContain('ride-row__type');
});

it('accents the calendar lockup by activity type', function () {
    $cases = [
        [ActivityType::KIDICALMASS, '--ride-accent: var(--color-kidical-red)'],
        [ActivityType::WORKSHOP, '--ride-accent: var(--color-kidical-green)'],
        [ActivityType::MEETING, '--ride-accent: var(--color-kidical-blue)'],
        [ActivityType::OTHER, '--ride-accent: var(--color-kidical-orange)'],
    ];

    foreach ($cases as [$type, $accent]) {
        $activity = Activity::factory()->create([
            'activity_type' => $type,
            'begin_date' => '2026-06-14 14:00',
        ]);

        $html = Blade::render(
            '<x-ride-day :period-key="$key" :rows="$rows" />',
            ['key' => '2026-06-14', 'rows' => [['item' => $activity]]],
        );

        expect($html)->toContain($accent);
    }
});

it('lets the ride win the accent when a day mixes types', function () {
    $ride = Activity::factory()->create(['activity_type' => ActivityType::KIDICALMASS, 'begin_date' => '2026-06-14 14:00']);
    $meeting = Activity::factory()->create(['activity_type' => ActivityType::MEETING, 'begin_date' => '2026-06-14 19:00']);

    $html = Blade::render(
        '<x-ride-day :period-key="$key" :rows="$rows" />',
        ['key' => '2026-06-14', 'rows' => [['item' => $meeting], ['item' => $ride]]],
    );

    expect($html)->toContain('--ride-accent: var(--color-kidical-red)');
});

it('shows the inline date only when showDate is set', function () {
    $ride = Activity::factory()->create(['begin_date' => '2026-06-14 14:00']);

    $withDate = Blade::render('<x-ride-row :activity="$activity" :show-date="true" />', ['activity' => $ride]);
    $without = Blade::render('<x-ride-row :activity="$activity" />', ['activity' => $ride]);

    expect($withDate)->toContain('ride-row__date');
    expect($without)->not->toContain('ride-row__date');
});

it('marks a flagship ride as featured without putting the star in the title', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Grote Kidical Mass Gent',
        'begin_date' => '2026-06-14 14:00',
    ]);

    $html = Blade::render('<x-ride-row :activity="$activity" />', ['activity' => $ride]);

    // The row keeps the featured hook, but the star now lives on the calendar lockup.
    expect($html)->toContain('ride-row--featured')
        ->not->toContain('ride-row__star')
        ->not->toContain('★')
        ->not->toContain('Uitgelicht');
});

it('shows the Grande star on the calendar lockup, not on a normal day', function () {
    $grande = Activity::factory()->create(['title_nl' => 'Grote Kidical Mass Gent', 'begin_date' => '2026-06-14 14:00']);
    $normal = Activity::factory()->create(['title_nl' => 'Kidical Mass Etterbeek', 'begin_date' => '2026-06-21 14:00']);

    $withStar = Blade::render('<x-ride-day :period-key="$key" :rows="$rows" />', ['key' => '2026-06-14', 'rows' => [['item' => $grande]]]);
    $withoutStar = Blade::render('<x-ride-day :period-key="$key" :rows="$rows" />', ['key' => '2026-06-21', 'rows' => [['item' => $normal]]]);

    expect($withStar)->toContain('ride-day__star');
    expect($withoutStar)->not->toContain('ride-day__star');
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
