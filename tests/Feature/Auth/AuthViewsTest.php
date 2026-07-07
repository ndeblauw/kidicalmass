<?php

// Branded auth shell (P-07): prove the volunteer door renders the NL shell —
// title, intro, role pill, collage. Rendering only; auth behaviour lives in
// AuthenticationTest, Fortify internals are not re-tested (thin-Auth rubric).

test('login page renders the branded NL shell', function () {
    $response = $this->get(route('login'));

    $response->assertOk()
        ->assertSee(__('auth.login_title'))
        ->assertSee(__('auth.login_intro'))
        ->assertSee(__('auth.role_pill'))
        ->assertSee('photo-collage');
});
