<?php

// The Home "Eén beweging" stat row shows the same figures as the /steun-ons
// deck (both read ImpactFigures), and drops a figure that has no real value.

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use App\Models\YearStat;
use App\Support\MovementStats;
use App\Support\SupportStats;

beforeEach(function () {
    app()->setLocale('nl');
});

it('shows the same numbers as the steun-ons deck, labelled with the reference year', function () {
    Group::factory()->count(27)->create(['invisible' => false]);
    Group::factory()->create(['invisible' => true]);
    YearStat::factory()->create(['year' => 2024, 'participants' => 5500]);
    Activity::factory()->count(2)->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => '2024-05-01 14:00',
        'is_published' => true,
        'author_id' => User::factory(),
    ]);

    $items = collect((new MovementStats)->items());

    expect($items->pluck('value')->sort()->values()->all())
        ->toBe(collect((new SupportStats)->cards())->pluck('value')->sort()->values()->all())
        ->and($items->pluck('value', 'key')->all())->toBe(['groups' => '27', 'rides' => '2', 'participants' => '5.500'])
        ->and($items->firstWhere('key', 'rides')['label'])->toBe(__('home.routes.movement.stats.rides', ['year' => 2024]));
});

it('drops rides and participants when the reference year has none', function () {
    Group::factory()->create(['invisible' => false]);
    YearStat::factory()->create(['year' => 2025, 'participants' => null]);

    expect(array_column((new MovementStats)->items(), 'key'))->toBe(['groups']);
});
