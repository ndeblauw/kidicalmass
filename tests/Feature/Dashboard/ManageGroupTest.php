<?php

use App\Livewire\Dashboard\ManageGroup;
use App\Models\Group;
use App\Models\User;
use Livewire\Livewire;

test('users can see their groups', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['name' => 'Test Group']);
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageGroup::class)
        ->assertSee('Test Group');
});

test('users can edit their group information', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['name' => 'Old Name', 'shortname' => 'oldname']);
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageGroup::class)
        ->call('edit')
        ->set('name', 'New Name')
        ->set('shortname', 'newname')
        ->call('save')
        ->assertDispatched('group-saved');

    expect($group->fresh()->name)->toBe('New Name');
    expect($group->fresh()->shortname)->toBe('newname');
});

test('users can update group zip code', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['zip' => null]);
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageGroup::class)
        ->call('edit')
        ->set('zip', '12345')
        ->call('save');

    expect($group->fresh()->zip)->toBe('12345');
});

test('users can update group dates', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageGroup::class)
        ->call('edit')
        ->set('started_at', '2025-01-01')
        ->set('ended_at', '2025-12-31')
        ->call('save');

    expect($group->fresh()->started_at->format('Y-m-d'))->toBe('2025-01-01');
    expect($group->fresh()->ended_at->format('Y-m-d'))->toBe('2025-12-31');
});

test('users cannot edit groups they are not member of', function () {
    $user = User::factory()->create();
    $userGroup = Group::factory()->create();
    $user->groups()->attach($userGroup);

    $otherGroup = Group::factory()->create();

    $this->actingAs($user);

    Livewire::test(ManageGroup::class)
        ->set('selectedGroupId', $otherGroup->id)
        ->call('loadGroup', $otherGroup->id)
        ->assertStatus(404);
});

test('group name is required', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageGroup::class)
        ->call('edit')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name']);
});

test('group shortname is required', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageGroup::class)
        ->call('edit')
        ->set('shortname', '')
        ->call('save')
        ->assertHasErrors(['shortname']);
});

test('group shortname must be unique', function () {
    $user = User::factory()->create();
    $group1 = Group::factory()->create(['shortname' => 'group1']);
    $group2 = Group::factory()->create(['shortname' => 'group2']);
    $user->groups()->attach($group1);

    $this->actingAs($user);

    Livewire::test(ManageGroup::class)
        ->call('edit')
        ->set('shortname', 'group2')
        ->call('save')
        ->assertHasErrors(['shortname']);
});

test('group ended_at must be after or equal to started_at', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageGroup::class)
        ->call('edit')
        ->set('started_at', '2025-12-31')
        ->set('ended_at', '2025-01-01')
        ->call('save')
        ->assertHasErrors(['ended_at']);
});

test('users can switch between their groups', function () {
    $user = User::factory()->create();
    $group1 = Group::factory()->create(['name' => 'Group 1']);
    $group2 = Group::factory()->create(['name' => 'Group 2']);
    $user->groups()->attach([$group1->id, $group2->id]);

    $this->actingAs($user);

    $component = Livewire::test(ManageGroup::class);

    $component->assertSee('Group 1');

    $component->set('selectedGroupId', $group2->id)
        ->assertSet('name', 'Group 2');
});

test('cancel button resets form', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['name' => 'Original Name']);
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageGroup::class)
        ->call('edit')
        ->set('name', 'Changed Name')
        ->call('cancel')
        ->assertSet('name', 'Original Name')
        ->assertSet('showForm', false);
});
