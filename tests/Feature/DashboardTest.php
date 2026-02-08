<?php

use App\Models\Group;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/dashboard')->assertOk();
});

test('dashboard lists the groups the user belongs to', function () {
    $user = User::factory()->create();
    $groupOne = Group::factory()->create(['name' => 'Group Alpha', 'shortname' => 'alpha']);
    $groupTwo = Group::factory()->create(['name' => 'Group Beta', 'shortname' => 'beta']);

    $user->groups()->attach([$groupOne->id, $groupTwo->id]);

    $this->actingAs($user);

    $this->get('/dashboard')
        ->assertOk()
        ->assertSee('Group Alpha')
        ->assertSee('Group Beta')
        ->assertSee('alpha')
        ->assertSee('beta');
});
