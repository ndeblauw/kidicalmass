<?php

use App\Livewire\ContactFormComponent;
use App\Mail\ContactFormSubmitted;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

it('mails the communications inbox when the contact form is submitted', function () {
    Mail::fake();
    config(['kidicalmass.mail.communications' => 'comms@example.com']);

    Livewire::test(ContactFormComponent::class)
        ->set('name', 'Test Ouder')
        ->set('email', 'ouder@gmail.com')
        ->set('message', 'Wanneer is de volgende rit?')
        ->call('submit')
        ->assertSet('submitted', true);

    Mail::assertSent(ContactFormSubmitted::class, fn ($mail) => $mail->hasTo('comms@example.com'));
});
