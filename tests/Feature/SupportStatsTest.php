<?php

// tests/Feature/SupportStatsTest.php
// Locks the dynamic proof deck on /steun-ons: groups + rides are derived live,
// participants come from a curated YearStat row. Cards with no honest value are
// omitted entirely (never a fake or zero number).

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use App\Models\YearStat;
use App\Support\SupportStats;

beforeEach(function () {
    // The page only ever renders under the NL locale; the labels live in lang/nl.
    app()->setLocale('nl');
});

/** Convenience: pull a single card by the substring of its (translated) label. */
function cardLabelled(array $cards, string $needle): ?array
{
    return collect($cards)->first(fn (array $card) => str_contains($card['label'], $needle));
}

function publishedRideOn(string $date): Activity
{
    return Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => $date,
        'published' => true,
        'author_id' => User::factory(),
    ]);
}

it('always shows a live local-groups card counting only visible groups', function () {
    Group::factory()->count(3)->create(['invisible' => false]);
    Group::factory()->create(['invisible' => true]);

    $card = cardLabelled((new SupportStats)->cards(), 'lokale groepen');

    expect($card)->not->toBeNull()
        ->and($card['value'])->toBe('3');
});

it('derives the reference year from the most recent YearStat', function () {
    YearStat::factory()->create(['year' => 2024, 'participants' => 1000]);
    YearStat::factory()->create(['year' => 2025, 'participants' => 5500]);

    $card = cardLabelled((new SupportStats)->cards(), 'fietsten mee');

    expect($card['label'])->toContain('2025')
        ->and($card['value'])->toBe('5.500');
});

it('counts only published rides in the reference year for the rides card', function () {
    YearStat::factory()->create(['year' => 2025, 'participants' => 5500]);
    publishedRideOn('2025-04-01 14:00');
    publishedRideOn('2025-09-01 14:00');
    Activity::factory()->create(['begin_date' => '2025-06-01 14:00', 'published' => false, 'author_id' => User::factory()]);
    publishedRideOn('2024-05-01 14:00'); // other year

    $card = cardLabelled((new SupportStats)->cards(), 'ritten in');

    expect($card)->not->toBeNull()
        ->and($card['value'])->toBe('2')
        ->and($card['label'])->toContain('2025');
});

it('omits the rides card when the reference year has no rides', function () {
    YearStat::factory()->create(['year' => 2025, 'participants' => 5500]);

    $cards = (new SupportStats)->cards();

    expect(cardLabelled($cards, 'ritten in'))->toBeNull()
        ->and(cardLabelled($cards, 'fietsten mee'))->not->toBeNull();
});

it('omits the participants card when the year has no participant count', function () {
    YearStat::factory()->create(['year' => 2025, 'participants' => null]);

    expect(cardLabelled((new SupportStats)->cards(), 'fietsten mee'))->toBeNull();
});

it('falls back to last completed year when no YearStat exists', function () {
    $lastYear = now()->subYear()->year;
    publishedRideOn(now()->subYear()->startOfYear()->addMonths(4)->toDateTimeString());

    $cards = (new SupportStats)->cards();

    expect(cardLabelled($cards, 'fietsten mee'))->toBeNull()
        ->and(cardLabelled($cards, 'ritten in'))->not->toBeNull()
        ->and(cardLabelled($cards, 'ritten in')['label'])->toContain((string) $lastYear);
});
