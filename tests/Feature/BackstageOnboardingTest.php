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
        ->assertSee('Stel je wachtwoord in');
});

it('activates the account and logs the volunteer in', function () {
    $this->post(route('backstage.activate', $this->group), [
        'password' => 'fietsfietsfiets',
        'password_confirmation' => 'fietsfietsfiets',
    ])->assertRedirect(route('backstage.welcome', $this->group));

    $this->assertAuthenticatedAs($this->morgane);
});

it('gates the backstage behind auth', function () {
    $this->get(route('backstage.home', $this->group))->assertRedirect();
    $this->get(route('backstage.welcome', $this->group))->assertRedirect();
    $this->get(route('backstage.team', $this->group))->assertRedirect();
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
