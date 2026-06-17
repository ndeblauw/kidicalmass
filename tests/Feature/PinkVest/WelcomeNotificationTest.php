<?php

use App\Models\Group;
use App\Models\User;
use App\Notifications\PinkVest\WelcomeNotification;

it('renders the welcome email with pink background', function () {
    $group = Group::create([
        'shortname' => 'gent',
        'name' => 'Gent',
        'zip' => '9000',
        'invisible' => false,
        'started_at' => now(),
    ]);

    $user = User::factory()->create(['name' => 'Lena Vandenberghe']);

    $mail = (new WelcomeNotification($group))->toMail($user);

    expect($mail->subject)
        ->toBe('Welkom bij de roze hesjes van Gent');

    $html = $mail->render();

    expect($html)
        ->toContain('#fce4ec')
        ->toContain('#E63A7B')
        ->toContain('Welkom bij de roze hesjes!')
        ->toContain('Lena')
        ->toContain('Kidical Mass Gent');
});

it('does not contain blue theme colors', function () {
    $group = Group::create([
        'shortname' => 'brussel',
        'name' => 'Brussel',
        'zip' => '1000',
        'invisible' => false,
        'started_at' => now(),
    ]);

    $user = User::factory()->create(['name' => 'Test Gebruiker']);

    $mail = (new WelcomeNotification($group))->toMail($user);
    $html = $mail->render();

    expect($html)
        ->not->toContain('#B7E7F0')
        ->not->toContain('#1d67cd');
});
