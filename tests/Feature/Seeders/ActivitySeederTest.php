<?php

use App\Models\Activity;
use App\Models\Group;
use Database\Seeders\ActivitySeeder;
use Database\Seeders\GroupSeeder;

test('group seeder creates agenda based groups', function () {
    $this->seed(GroupSeeder::class);

    expect(Group::query()->where('shortname', 'schaerbeek-schaarbeek')->exists())->toBeTrue()
        ->and(Group::query()->where('shortname', 'watermael-boitsfort-auderghem')->exists())->toBeTrue()
        ->and(Group::query()->where('shortname', 'antwerp')->exists())->toBeFalse();
});

test('activity seeder creates agenda activities and links groups', function () {
    $this->seed(GroupSeeder::class);
    $this->seed(ActivitySeeder::class);

    $activity = Activity::query()
        ->where('title_nl', 'Kidical Mass Schaerbeek - Schaarbeek')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->activity_type->value)->toBe('kidicalmass')
        ->and($activity->groups()->where('shortname', 'schaerbeek-schaarbeek')->exists())->toBeTrue();
});
