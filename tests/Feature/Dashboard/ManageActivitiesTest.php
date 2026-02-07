<?php

use App\Livewire\Dashboard\ManageActivities;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Livewire\Livewire;

test('users can see activities from their groups', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $activity = Activity::factory()->create(['author_id' => $user->id]);
    $activity->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageActivities::class)
        ->assertSee($activity->title);
});

test('users cannot see activities from other groups', function () {
    $user = User::factory()->create();
    $userGroup = Group::factory()->create();
    $user->groups()->attach($userGroup);

    $otherGroup = Group::factory()->create();
    $activity = Activity::factory()->create();
    $activity->groups()->attach($otherGroup);

    $this->actingAs($user);

    Livewire::test(ManageActivities::class)
        ->assertDontSee($activity->title);
});

test('users can create activities', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageActivities::class)
        ->set('title', 'New Activity')
        ->set('description', 'Activity description here')
        ->set('location', 'City Center')
        ->set('begin_date', '2026-03-01 10:00:00')
        ->call('save')
        ->assertDispatched('activity-saved');

    expect(Activity::where('title', 'New Activity')->exists())->toBeTrue();
    expect(Activity::where('title', 'New Activity')->first()->groups->contains($group))->toBeTrue();
});

test('users can edit activities from their groups', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $activity = Activity::factory()->create(['author_id' => $user->id]);
    $activity->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageActivities::class)
        ->call('edit', $activity->id)
        ->set('title', 'Updated Title')
        ->set('description', 'Updated description')
        ->call('save');

    expect($activity->fresh()->title)->toBe('Updated Title');
    expect($activity->fresh()->description)->toBe('Updated description');
});

test('users can delete activities from their groups', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $activity = Activity::factory()->create(['author_id' => $user->id]);
    $activity->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageActivities::class)
        ->call('delete', $activity->id)
        ->assertDispatched('activity-deleted');

    expect(Activity::find($activity->id))->toBeNull();
});

test('users cannot edit activities from other groups', function () {
    $user = User::factory()->create();
    $userGroup = Group::factory()->create();
    $user->groups()->attach($userGroup);

    $otherGroup = Group::factory()->create();
    $activity = Activity::factory()->create();
    $activity->groups()->attach($otherGroup);

    $this->actingAs($user);

    Livewire::test(ManageActivities::class)
        ->call('edit', $activity->id)
        ->assertStatus(404);
});

test('users cannot delete activities from other groups', function () {
    $user = User::factory()->create();
    $userGroup = Group::factory()->create();
    $user->groups()->attach($userGroup);

    $otherGroup = Group::factory()->create();
    $activity = Activity::factory()->create();
    $activity->groups()->attach($otherGroup);

    $this->actingAs($user);

    Livewire::test(ManageActivities::class)
        ->call('delete', $activity->id)
        ->assertStatus(404);
});

test('activity title is required', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageActivities::class)
        ->set('title', '')
        ->set('description', 'Some description')
        ->set('begin_date', '2026-03-01 10:00:00')
        ->call('save')
        ->assertHasErrors(['title']);
});

test('activity description is required', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageActivities::class)
        ->set('title', 'Some title')
        ->set('description', '')
        ->set('begin_date', '2026-03-01 10:00:00')
        ->call('save')
        ->assertHasErrors(['description']);
});

test('activity begin_date is required', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageActivities::class)
        ->set('title', 'Some title')
        ->set('description', 'Some description')
        ->set('begin_date', '')
        ->call('save')
        ->assertHasErrors(['begin_date']);
});

test('activity end_date must be after or equal to begin_date', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageActivities::class)
        ->set('title', 'Some title')
        ->set('description', 'Some description')
        ->set('begin_date', '2026-03-01 10:00:00')
        ->set('end_date', '2026-02-28 10:00:00')
        ->call('save')
        ->assertHasErrors(['end_date']);
});
