<?php

use App\Models\Activity;
use Tests\TestCase;

uses(TestCase::class);

it('exposes ride display accessors that delegate to RideDate', function () {
    app()->setLocale('nl');
    // Override author_id with a raw value to prevent User::factory() DB writes under make().
    $ride = Activity::factory()->make([
        'title_nl' => 'Kidical Mass Etterbeek',
        'title_fr' => 'Kidical Mass Etterbeek (fr)',
        'begin_date' => '2026-06-14 14:00',
        'author_id' => 1,
    ]);

    expect($ride->timeLabel)->toBe('14u');
    expect($ride->dateShort)->toBe('zo 14 jun.');
    expect($ride->dateFull)->toBe('zondag 14 juni');
    expect($ride->dateMonthYear)->toBe('juni 2026');
    expect($ride->title)->toBe('Kidical Mass Etterbeek');
});

it('flags a Grande / Grote Kidical Mass as grande', function () {
    $make = fn (string $nl) => Activity::factory()->make(['title_nl' => $nl, 'author_id' => 1]);

    expect($make('Grande Kidical Mass: najaarseditie')->isGrande())->toBeTrue();
    expect($make('Grote Kidical Mass Brussel')->isGrande())->toBeTrue();
    expect($make('Kidical Mass Etterbeek')->isGrande())->toBeFalse();
});

it('picks the French title when the locale is fr', function () {
    app()->setLocale('fr');
    // Override author_id with a raw value to prevent User::factory() DB writes under make().
    $ride = Activity::factory()->make([
        'title_nl' => 'NL titel',
        'title_fr' => 'Titre FR',
        'author_id' => 1,
    ]);

    expect($ride->title)->toBe('Titre FR');
});
