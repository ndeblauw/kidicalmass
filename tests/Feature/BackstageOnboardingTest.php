<?php

use App\Models\Group;
use App\Models\User;
use Database\Seeders\PrototypeOnboardingSeeder;

/*
 * Smoke coverage for the pink-vest onboarding prototype (Mon 8 June demo).
 * Verifies each surface in the click-through renders and that the shallow
 * activation actually logs the volunteer in.
 */

beforeEach(function () {
    $this->seed(PrototypeOnboardingSeeder::class);
    $this->group = Group::where('shortname', 'oudergem')->firstOrFail();
    $this->morgane = User::where('email', 'morgane@example.test')->firstOrFail();
});

it('renders the invite email preview', function () {
    $this->get(route('prototype.mail.invite'))
        ->assertOk()
        ->assertSee('Welkom bij de roze hesjes')
        ->assertSee('Activeer je account');
});

it('renders the activation screen', function () {
    $this->get(route('backstage.activate', $this->group))
        ->assertOk()
        ->assertSee('Account activeren');
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

    $this->get(route('backstage.welcome', $this->group))
        ->assertOk()
        ->assertSee('roze hesje')
        ->assertSee('Je eerste rit, stap voor stap')
        ->assertSee('startspeech');

    $this->get(route('backstage.home', $this->group))
        ->assertOk()
        ->assertSee('Materiaal')
        ->assertSee('Afsprakencharter');

    $this->get(route('backstage.team', $this->group))
        ->assertOk()
        ->assertSee('De roze hesjes van Oudergem')
        ->assertSee('Thomas Maes');
});
