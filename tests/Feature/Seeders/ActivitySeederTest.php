<?php

use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Database\Seeders\ActivitySeeder;
use Database\Seeders\GroupSeeder;

test('group seeder creates expected agenda groups and does not create legacy city groups', function () {
    $this->seed(GroupSeeder::class);

    $legacyGroupsThatShouldNotExist = ['antwerp', 'gent', 'leuven', 'hasselt', 'brugge', 'brussels', 'liege', 'arlon', 'nivelles'];

    expect(Group::query()->where('shortname', 'schaerbeek-schaarbeek')->exists())->toBeTrue()
        ->and(Group::query()->where('shortname', 'watermael-boitsfort-auderghem')->exists())->toBeTrue()
        ->and(Group::query()->where('shortname', 'mechelen')->exists())->toBeTrue()
        ->and(Group::query()->where('shortname', 'mons')->exists())->toBeTrue()
        ->and(Group::query()->where('shortname', 'namur')->exists())->toBeTrue()
        ->and(Group::query()->whereIn('shortname', $legacyGroupsThatShouldNotExist)->count())->toBe(0);
});

test('activity seeder creates agenda activities and links groups', function () {
    $this->seed(GroupSeeder::class);
    User::factory()->create();
    $this->seed(ActivitySeeder::class);

    $activity = Activity::query()
        ->where('title_nl', 'Kidical Mass Schaerbeek - Schaarbeek')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->activity_type->value)->toBe('kidicalmass')
        ->and($activity->groups()->where('shortname', 'schaerbeek-schaarbeek')->exists())->toBeTrue();
});
