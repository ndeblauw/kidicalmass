<?php

use App\Models\Group;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    // Clean the database
    User::query()->delete();
    Group::query()->delete();
});

test('user can be assigned to multiple groups', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);

    $group1 = Group::create([
        'shortname' => 'group1',
        'name' => 'Group 1',
        'started_at' => now(),
    ]);

    $group2 = Group::create([
        'shortname' => 'group2',
        'name' => 'Group 2',
        'started_at' => now(),
    ]);

    // Attach groups to user
    $user->groups()->attach([$group1->id, $group2->id]);

    // Verify the relationship
    expect($user->groups)->toHaveCount(2)
        ->and($user->groups->pluck('id')->toArray())->toContain($group1->id, $group2->id);
});

test('user groups relationship is bidirectional', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);

    $group = Group::create([
        'shortname' => 'group1',
        'name' => 'Group 1',
        'started_at' => now(),
    ]);

    // Attach user to group
    $group->users()->attach($user->id);

    // Verify the relationship works both ways
    expect($user->fresh()->groups)->toHaveCount(1)
        ->and($group->fresh()->users)->toHaveCount(1)
        ->and($group->users->first()->id)->toBe($user->id);
});

test('impersonation session is stored correctly', function () {
    $admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);

    $targetUser = User::create([
        'name' => 'Target User',
        'email' => 'target@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);

    // Simulate impersonation
    actingAs($admin);
    session()->put('impersonate_from', $admin->id);

    // Check that session contains the original user ID
    expect(session()->has('impersonate_from'))->toBeTrue()
        ->and(session()->get('impersonate_from'))->toBe($admin->id);
});
