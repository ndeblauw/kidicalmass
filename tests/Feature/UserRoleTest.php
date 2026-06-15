<?php

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Blade;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    User::query()->delete();
    Group::query()->delete();
});

// ── Role helpers ──

it('detects superadmin', function () {
    $user = User::factory()->create(['superadmin' => false]);
    expect($user->isSuperAdmin())->toBeFalse();

    $admin = User::factory()->create(['superadmin' => true]);
    expect($admin->isSuperAdmin())->toBeTrue();
});

it('detects pinkvest within a group', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $user = User::factory()->create();
    $user->groups()->attach($group, ['role' => 'pinkvest']);

    expect($user->isPinkVestOf($group))->toBeTrue();
    expect($user->isCaptainOf($group))->toBeFalse();
});

it('detects captain within a group', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $user = User::factory()->create();
    $user->groups()->attach($group, ['role' => 'captain']);

    expect($user->isCaptainOf($group))->toBeTrue();
});

it('captain inherits pinkvest rights', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $user = User::factory()->create();
    $user->groups()->attach($group, ['role' => 'captain']);

    expect($user->isPinkVestOf($group))->toBeTrue();
});

it('unconnected user has no roles', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $user = User::factory()->create();

    expect($user->isPinkVestOf($group))->toBeFalse();
    expect($user->isCaptainOf($group))->toBeFalse();
});

it('superadmin is captain for every group', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $admin = User::factory()->create(['superadmin' => true]);

    expect($admin->isCaptainOf($group))->toBeTrue();
    expect($admin->isPinkVestOf($group))->toBeTrue();
});

it('pinkvest in one group does not affect another group', function () {
    $groupA = Group::create(['shortname' => 'a', 'name' => 'Group A', 'started_at' => now()]);
    $groupB = Group::create(['shortname' => 'b', 'name' => 'Group B', 'started_at' => now()]);
    $user = User::factory()->create();
    $user->groups()->attach($groupA, ['role' => 'pinkvest']);

    expect($user->isPinkVestOf($groupA))->toBeTrue();
    expect($user->isPinkVestOf($groupB))->toBeFalse();
});

// ── Blade directives ──

it('@pinkvest directive returns true for a pinkvest user', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $user = User::factory()->create();
    $user->groups()->attach($group, ['role' => 'pinkvest']);
    actingAs($user);

    expect(Blade::check('pinkvest', $group))->toBeTrue();
});

it('@captain directive returns true for a captain', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $user = User::factory()->create();
    $user->groups()->attach($group, ['role' => 'captain']);
    actingAs($user);

    expect(Blade::check('captain', $group))->toBeTrue();
});

it('@pinkvest directive returns false for unconnected user', function () {
    $group = Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()]);
    $user = User::factory()->create();
    actingAs($user);

    expect(Blade::check('pinkvest', $group))->toBeFalse();
});

it('@admin directive returns true for superadmin', function () {
    $user = User::factory()->create(['superadmin' => true]);
    actingAs($user);

    expect(Blade::check('admin'))->toBeTrue();
});

it('@admin directive returns false for regular user', function () {
    $user = User::factory()->create(['superadmin' => false]);
    actingAs($user);

    expect(Blade::check('admin'))->toBeFalse();
});

// ── Login-as shortcuts (non-production) ──

it('login as user shortcut works', function () {
    get(route('login.as', 'user'))
        ->assertRedirect('/dashboard');

    expect(auth()->check())->toBeTrue()
        ->and(auth()->user()->email)->toBe('user@kidi.be')
        ->and(auth()->user()->isSuperAdmin())->toBeFalse();
});

it('login as admin shortcut works and sets superadmin', function () {
    get(route('login.as', 'admin'))
        ->assertRedirect('/dashboard');

    expect(auth()->check())->toBeTrue()
        ->and(auth()->user()->email)->toBe('admin@kidi.be')
        ->and(auth()->user()->isSuperAdmin())->toBeTrue();
});

it('login as pinkvest creates group connection with pinkvest role', function () {
    get(route('login.as', 'pinkvest'))
        ->assertRedirect('/dashboard');

    $user = auth()->user();
    expect($user)->not->toBeNull();

    $group = Group::where('shortname', 'demo-chapter')->firstOrFail();

    expect($user->isPinkVestOf($group))->toBeTrue();

it('login as captain creates group connection with captain role', function () {
    get(route('login.as', 'captain'))
        ->assertRedirect('/dashboard');

    $user = auth()->user();
    $group = Group::where('shortname', 'demo-chapter')->first();

    expect($user->isCaptainOf($group))->toBeTrue();
});

it('login as invalid role returns 404', function () {
    get('/login/as/nonexistent')->assertNotFound();
});
