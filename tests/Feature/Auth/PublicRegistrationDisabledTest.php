<?php

use function Pest\Laravel\get;

it('does not expose public registration', function () {
    get('/register')->assertNotFound();
});

it('still serves the login page without a register link', function () {
    get('/login')
        ->assertOk()
        ->assertDontSee(url('/register'), escape: false);
});
