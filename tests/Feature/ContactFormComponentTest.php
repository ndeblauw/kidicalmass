<?php

use App\Livewire\ContactFormComponent;
use App\Mail\ContactFormSubmitted;
use App\Models\ContactForm;
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

it('prefixes the chosen topic into the stored message', function () {
    Mail::fake();

    Livewire::test(ContactFormComponent::class)
        ->set('name', 'Test Journalist')
        ->set('email', 'redactie@gmail.com')
        ->set('topic', 'pers')
        ->set('message', 'Ik zoek cijfers over de ritten van 2025.')
        ->call('submit')
        ->assertSet('submitted', true);

    expect(ContactForm::sole()->message)
        ->toBe("Onderwerp: Pers\n\nIk zoek cijfers over de ritten van 2025.");
});
