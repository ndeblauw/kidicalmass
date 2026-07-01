<?php

use App\Models\Group;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    User::query()->delete();
    Group::query()->delete();
});

// ── Filament access helpers ──

it('superadmin can access filament', function () {
    $user = User::factory()->create(['superadmin' => true]);

    expect($user->canAccessFilament())->toBeTrue();
});

it('captain can access filament', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $user = User::factory()->create(['superadmin' => false]);
    $user->groups()->attach($group, ['role' => 'captain']);

    expect($user->canAccessFilament())->toBeTrue();
});

it('pinkvest cannot access filament', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $user = User::factory()->create(['superadmin' => false]);
    $user->groups()->attach($group, ['role' => 'pinkvest']);

    expect($user->canAccessFilament())->toBeFalse();
});

it('regular user cannot access filament', function () {
    $user = User::factory()->create(['superadmin' => false]);

    expect($user->canAccessFilament())->toBeFalse();
});

// ── isCaptain / isPinkVest helpers ──

it('isCaptain returns true when user has captain role', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $user = User::factory()->create();
    $user->groups()->attach($group, ['role' => 'captain']);

    expect($user->isCaptain())->toBeTrue();
});

it('isCaptain returns false for pinkvest', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $user = User::factory()->create();
    $user->groups()->attach($group, ['role' => 'pinkvest']);

    expect($user->isCaptain())->toBeFalse();
});

it('isCaptain returns false for regular user', function () {
    $user = User::factory()->create();
    expect($user->isCaptain())->toBeFalse();
});

it('isPinkVest returns true for pinkvest and captain', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $pinkvest = User::factory()->create();
    $pinkvest->groups()->attach($group, ['role' => 'pinkvest']);
    $captain = User::factory()->create();
    $captain->groups()->attach($group, ['role' => 'captain']);

    expect($pinkvest->isPinkVest())->toBeTrue();
    expect($captain->isPinkVest())->toBeTrue();
});

it('isPinkVest returns false for regular user', function () {
    $user = User::factory()->create();
    expect($user->isPinkVest())->toBeFalse();
});

// ── Admin page access ──

it('filament admin returns 403 for regular user', function () {
    $user = User::factory()->create(['superadmin' => false]);

    actingAs($user)
        ->get('/admin')
        ->assertForbidden();
});

it('filament admin returns 403 for pinkvest', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $user = User::factory()->create();
    $user->groups()->attach($group, ['role' => 'pinkvest']);

    actingAs($user)
        ->get('/admin')
        ->assertForbidden();
});

it('admin redirects to login for guest', function () {
    get('/admin')->assertRedirect(route('login'));
});

it('captain passes middleware via canAccessFilament', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $user = User::factory()->create();
    $user->groups()->attach($group, ['role' => 'captain']);

    expect($user->canAccessFilament())->toBeTrue()
        ->and($user->isCaptain())->toBeTrue();
});

it('superadmin passes middleware via canAccessFilament', function () {
    $user = User::factory()->create(['superadmin' => true]);

    expect($user->canAccessFilament())->toBeTrue();
});
