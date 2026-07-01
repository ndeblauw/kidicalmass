<?php

use App\Livewire\GroupMemberManager;
use App\Models\Group;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->group = Group::factory()->create(['name' => 'Test Group']);
    $this->user = User::factory()->create(['name' => 'Alice', 'email' => 'alice@example.com']);
    $this->otherUser = User::factory()->create(['name' => 'Bob', 'email' => 'bob@example.com']);

    $this->group->users()->attach($this->user, ['role' => null, 'is_public' => true]);
    $this->group->users()->attach($this->otherUser, ['role' => 'pinkvest', 'is_public' => false]);
});

it('renders all group members by default', function () {
    Livewire::test(GroupMemberManager::class, ['group' => $this->group])
        ->assertSee('Alice')
        ->assertSee('Bob');
});

it('filters members by search query', function () {
    Livewire::test(GroupMemberManager::class, ['group' => $this->group])
        ->set('search', 'Alice')
        ->assertSee('Alice')
        ->assertDontSee('Bob');
});

it('filters members by email', function () {
    Livewire::test(GroupMemberManager::class, ['group' => $this->group])
        ->set('search', 'bob@example.com')
        ->assertSee('Bob')
        ->assertDontSee('Alice');
});

it('toggles a member role to pinkvest', function () {
    Livewire::test(GroupMemberManager::class, ['group' => $this->group])
        ->call('toggleRole', $this->user->id, 'pinkvest');

    $freshUser = $this->user->fresh();
    expect($freshUser->groups()->wherePivot('role', 'pinkvest')->exists())->toBeTrue();
});

it('toggles a member role to captain', function () {
    Livewire::test(GroupMemberManager::class, ['group' => $this->group])
        ->call('toggleRole', $this->user->id, 'captain');

    $freshUser = $this->user->fresh();
    expect($freshUser->groups()->wherePivot('role', 'captain')->exists())->toBeTrue();
});

it('clears a member role back to interested', function () {
    Livewire::test(GroupMemberManager::class, ['group' => $this->group])
        ->call('toggleRole', $this->otherUser->id, '');

    $freshUser = $this->otherUser->fresh();
    expect($freshUser->groups()->whereNull('role')->exists())->toBeTrue();
});
