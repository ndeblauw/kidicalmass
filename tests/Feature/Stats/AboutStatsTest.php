<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\YearStat;
use App\Support\AboutStats;

it('counts groups and all-time published parades live', function () {
    Group::factory()->count(2)->create(['invisible' => false]);
    Activity::factory()->count(3)->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'is_published' => true,
    ]);
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'is_published' => false,
    ]);

    $labels = collect(app(AboutStats::class)->cards())->pluck('value', 'label');

    expect($labels[__('about.stats.groups')])->toBe('2')
        ->and($labels[__('about.stats.rides')])->toBe('3');
});

it('reads volunteers and participants from the latest curated year and formats them per locale', function () {
    app()->setLocale('nl');
    YearStat::create(['year' => 2024, 'participants' => 9999, 'volunteers' => 1]);
    YearStat::create(['year' => 2025, 'participants' => 5500, 'volunteers' => 120]);

    $cards = collect(app(AboutStats::class)->cards());

    expect($cards->firstWhere('label', __('about.stats.volunteers'))['value'])->toBe('120')
        ->and($cards->firstWhere('label', __('about.stats.participants', ['year' => 2025]))['value'])->toBe('5.500');
});

it('omits any metric without an honest value', function () {
    // No rides, no year stats: only the groups card remains.
    $cards = app(AboutStats::class)->cards();

    expect($cards)->toHaveCount(1)
        ->and($cards[0]['label'])->toBe(__('about.stats.groups'));
});

it('highlights groups and participants for the hub, falling back to volunteers', function () {
    Group::factory()->count(2)->create(['invisible' => false]);
    YearStat::create(['year' => 2025, 'participants' => null, 'volunteers' => 120]);

    expect(collect(app(AboutStats::class)->highlights())->pluck('value', 'label')->all())->toBe([
        __('about.stats.groups') => '2',
        __('about.stats.volunteers') => '120',
    ]);

    YearStat::create(['year' => 2026, 'participants' => 5500, 'volunteers' => 150]);

    expect(collect(app(AboutStats::class)->highlights())->pluck('label')->all())->toBe([
        __('about.stats.groups'),
        __('about.stats.participants', ['year' => 2026]),
    ]);
});
