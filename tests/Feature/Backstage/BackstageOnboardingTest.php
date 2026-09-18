<?php

use App\Models\Group;
use App\Models\User;
use Database\Seeders\PrototypeOnboardingSeeder;

beforeEach(function () {
    $this->seed(PrototypeOnboardingSeeder::class);
    $this->group = Group::where('shortname', 'oudergem')->firstOrFail();
    $this->morgane = User::where('email', 'morgane@example.test')->firstOrFail();
});

it('activates the account in one click and logs the volunteer in', function () {
    $this->post(route('backstage.activate', $this->group))
        ->assertRedirect(route('backstage.welcome', $this->group));

    $this->assertAuthenticatedAs($this->morgane);
});

it('opens the backstage for a guest by signing them in as the demo volunteer', function () {
    $this->get(route('backstage.home', $this->group))->assertOk();
    $this->assertAuthenticatedAs($this->morgane);
});

it('renders the welcome, home and roster for a logged-in volunteer', function () {
    $this->actingAs($this->morgane);

    $this->get(route('backstage.welcome', $this->group))->assertOk();
    $this->get(route('backstage.home', $this->group))->assertOk();
    $this->get(route('backstage.team', $this->group))->assertOk();
});
