<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function (): void {
    $this->withoutVite();
});

it('lets a signed-in member reach settings and log out from the mobile menu', function () {
    actingAs(User::factory()->create())
        ->get('/nl')
        ->assertOk()
        ->assertSee('class="mobile-menu__account"', escape: false)
        ->assertSee(route('settings'), escape: false)
        ->assertSee('action="'.route('logout').'"', escape: false);
});

it('labels the account links in the page language', function () {
    actingAs(User::factory()->create())
        ->get('/fr')
        ->assertOk()
        ->assertSee(__('nav.settings', [], 'fr'))
        ->assertSee(__('auth.logout', [], 'fr'))
        ->assertDontSee(__('nav.settings', [], 'nl'));
});

it('shows no account links to visitors', function () {
    get('/nl')
        ->assertOk()
        ->assertDontSee('mobile-menu__account', escape: false);
});
