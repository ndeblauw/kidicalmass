<?php

use App\Support\RideDate;
use Illuminate\Support\Carbon;
use Tests\TestCase;

uses(TestCase::class);

it('formats time in Belgian Dutch, dropping :00 on whole hours', function () {
    app()->setLocale('nl');
    expect(RideDate::time(Carbon::parse('2026-06-14 14:00')))->toBe('14u');
    expect(RideDate::time(Carbon::parse('2026-06-14 14:30')))->toBe('14u30');
    expect(RideDate::time(Carbon::parse('2026-06-14 09:05')))->toBe('9u05');
});

it('formats time in Belgian French with an h separator', function () {
    app()->setLocale('fr');
    expect(RideDate::time(Carbon::parse('2026-06-14 14:00')))->toBe('14h');
    expect(RideDate::time(Carbon::parse('2026-06-14 14:30')))->toBe('14h30');
});

it('formats short, full and month-year dates per locale', function () {
    app()->setLocale('nl');
    expect(RideDate::short('2026-06-14'))->toBe('zo 14 jun.');
    expect(RideDate::full('2026-06-14'))->toBe('zondag 14 juni');
    expect(RideDate::monthYear('2026-06-14'))->toBe('juni 2026');

    app()->setLocale('fr');
    expect(RideDate::short('2026-06-14'))->toBe('di 14 juin');
    expect(RideDate::full('2026-06-14'))->toBe('dimanche 14 juin');
    expect(RideDate::monthYear('2026-06-14'))->toBe('juin 2026');
});

it('returns lowercase output so casing stays a CSS concern', function () {
    app()->setLocale('nl');
    $full = RideDate::full('2026-06-14');
    expect($full)->toBe(mb_strtolower($full));
});

it('builds the calendar-lockup parts with the full day name', function () {
    app()->setLocale('nl');
    $rail = RideDate::rail('2026-06-14');
    expect($rail['num'])->toBe('14');
    expect($rail['month'])->toBe('juni');
    expect($rail['day'])->toBe('zondag');
    expect($rail['rotation'])->toBeFloat();

    app()->setLocale('fr');
    expect(RideDate::rail('2026-06-14')['day'])->toBe('dimanche');
});

it('seeds a stable, readable rotation per date', function () {
    expect(RideDate::railRotation('2026-06-14'))->toBe(RideDate::railRotation('2026-06-14')); // stable
    expect(abs(RideDate::railRotation('2026-06-14')))->toBeLessThanOrEqual(5.0);               // readable bounds
    expect(RideDate::railRotation('2026-06-15'))->not->toBe(RideDate::railRotation('2026-06-14')); // varies by date
});
