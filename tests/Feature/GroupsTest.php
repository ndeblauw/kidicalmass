<?php

use App\Models\Group;

use function Pest\Laravel\get;

beforeEach(function () {
    // Ensure the database is clean before each test
    Group::query()->delete();
});

test('visible scope filters invisible groups', function () {
    // Create visible groups
    $visibleGroup1 = Group::create([
        'shortname' => 'visible1',
        'name' => 'Visible Group 1',
        'invisible' => false,
        'started_at' => now(),
    ]);

    $visibleGroup2 = Group::create([
        'shortname' => 'visible2',
        'name' => 'Visible Group 2',
        'invisible' => false,
        'started_at' => now(),
    ]);

    // Create invisible groups
    Group::create([
        'shortname' => 'invisible1',
        'name' => 'Invisible Group 1',
        'invisible' => true,
        'started_at' => now(),
    ]);

    Group::create([
        'shortname' => 'invisible2',
        'name' => 'Invisible Group 2',
        'invisible' => true,
        'started_at' => now(),
    ]);

    // Test visible scope
    $visibleGroups = Group::visible()->get();

    expect($visibleGroups)->toHaveCount(2)
        ->and($visibleGroups->pluck('id')->toArray())->toContain($visibleGroup1->id, $visibleGroup2->id);
});

test('groups index only shows visible groups', function () {
    // Create visible groups
    Group::create([
        'shortname' => 'visible1',
        'name' => 'Visible Group 1',
        'invisible' => false,
        'started_at' => now(),
    ]);

    // Create invisible groups
    Group::create([
        'shortname' => 'invisible1',
        'name' => 'Invisible Group 1',
        'invisible' => true,
        'started_at' => now(),
    ]);

    // Visit groups index
    $response = get(route('groups.index'));

    $response->assertOk()
        ->assertSee('Visible Group 1')
        ->assertDontSee('Invisible Group 1');
});

test('invisible field defaults to false', function () {
    $group = Group::create([
        'shortname' => 'test',
        'name' => 'Test Group',
        'started_at' => now(),
    ]);

    // Refresh to get the database default
    $group->refresh();

    expect($group->invisible)->toBeFalse();
});
