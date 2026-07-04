<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Support\RozeHub\OverviewMoment;
use Illuminate\Support\Carbon;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(fn () => Carbon::setTestNow('2026-07-01 10:00:00')); // a Wednesday
afterEach(fn () => Carbon::setTestNow());

function ride(string $beginDate, ActivityType $type = ActivityType::KIDICALMASS): Activity
{
    return new Activity(['begin_date' => $beginDate, 'activity_type' => $type]);
}

test('moment priority: welcome beats recap beats pre-ride beats default', function () {
    $recap = ride('2026-06-28 14:00:00');
    $next = ride('2026-07-05 14:00:00');

    expect(OverviewMoment::resolve(true, $recap, $next))->toBe('welcome')
        ->and(OverviewMoment::resolve(false, $recap, $next))->toBe('recap')
        ->and(OverviewMoment::resolve(false, null, $next))->toBe('pre-ride')
        ->and(OverviewMoment::resolve(false, null, null))->toBe('default');
});

test('a far-away or non-ride next activity does not make a pre-ride moment', function () {
    expect(OverviewMoment::resolve(false, null, ride('2026-07-20 14:00:00')))->toBe('default')
        ->and(OverviewMoment::resolve(false, null, ride('2026-07-05 19:30:00', ActivityType::MEETING)))->toBe('default');
});

test('countdown wording follows the nights until the ride', function () {
    expect(OverviewMoment::countdownLabel(ride('2026-07-01 14:00:00')))->toBe('Vandaag rijden we!')
        ->and(OverviewMoment::countdownLabel(ride('2026-07-02 14:00:00')))->toBe('Morgen is het zover.')
        ->and(OverviewMoment::countdownLabel(ride('2026-07-06 14:00:00')))->toBe('Nog 5 nachtjes slapen.')
        ->and(OverviewMoment::countdownLabel(ride('2026-07-08 14:00:00')))->toBe('Nog 7 nachtjes slapen.');
});

test('countdown stays silent beyond a week and for meetings', function () {
    expect(OverviewMoment::countdownLabel(ride('2026-07-09 14:00:00')))->toBeNull()
        ->and(OverviewMoment::countdownLabel(ride('2026-07-02 19:30:00', ActivityType::MEETING)))->toBeNull();
});
