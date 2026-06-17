<?php

use App\Models\User;

test('guest requests are ignored', function () {
    $this->get(route('home', ['locale' => 'nl']));

    expect(session('last_active_on'))->toBeNull();
});

test('authenticated user gets last_active_on set on first request', function () {
    $user = User::factory()->create(['last_active_on' => null]);

    $this->actingAs($user)->get(route('home', ['locale' => 'nl']));

    expect(session('last_active_on'))->toBe(now()->toDateString());
    expect($user->fresh()->last_active_on->toDateString())->toBe(now()->toDateString());
});

test('subsequent requests on the same day do not touch the database', function () {
    $user = User::factory()->create([
        'last_active_on' => now(),
        'updated_at' => now()->subHour(),
    ]);

    $originalUpdatedAt = $user->updated_at;

    $this->actingAs($user)->get(route('home', ['locale' => 'nl']));

    expect(session('last_active_on'))->toBe(now()->toDateString());
    expect($user->fresh()->updated_at->timestamp)->toBe($originalUpdatedAt->timestamp);
});

test('user tracked on a previous day gets updated on new day', function () {
    $user = User::factory()->create([
        'last_active_on' => now()->subDay(),
    ]);

    $this->actingAs($user)->get(route('home', ['locale' => 'nl']));

    expect(session('last_active_on'))->toBe(now()->toDateString());
    expect($user->fresh()->last_active_on->toDateString())->toBe(now()->toDateString());
});

test('user already tracked today gets session filled without database write', function () {
    $user = User::factory()->create([
        'last_active_on' => now(),
        'updated_at' => now()->subHour(),
    ]);

    $originalUpdatedAt = $user->updated_at;

    $this->actingAs($user)->get(route('home', ['locale' => 'nl']));

    expect(session('last_active_on'))->toBe(now()->toDateString());
    expect($user->fresh()->updated_at->timestamp)->toBe($originalUpdatedAt->timestamp);
});
