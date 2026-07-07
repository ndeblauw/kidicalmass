<?php

// Branded auth shell (P-07): prove the volunteer door renders the NL shell —
// title, intro, role pill, collage. Rendering only; auth behaviour lives in
// AuthenticationTest, Fortify internals are not re-tested (thin-Auth rubric).

test('login page renders the branded NL shell', function () {
    $response = $this->get(route('login'));

    $response->assertOk()
        ->assertSee(__('auth.login_title'))
        ->assertSee(__('auth.role_pill'))
        ->assertSee('photo-collage');
});

test('forgot-password page renders in the branded shell', function () {
    $this->get(route('password.request'))
        ->assertOk()
        ->assertSee(__('auth.forgot_title'))
        ->assertSee(__('auth.role_pill'));
});

test('reset-password page renders in the branded shell', function () {
    $this->get(route('password.reset', 'dummy-token'))
        ->assertOk()
        ->assertSee(__('auth.reset_title'));
});
