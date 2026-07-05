<?php

use App\Livewire\ChapterVolunteerSignup;
use App\Mail\ContactFormSubmitted;
use App\Models\ContactForm;
use App\Models\Group;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

it('submits a chapter volunteer enquiry tagged with the group and chosen roles', function () {
    Mail::fake();
    config(['kidicalmass.mail.communications' => 'comms@example.com']);
    $group = Group::factory()->create(['name' => 'Schaarbeek']);

    Livewire::test(ChapterVolunteerSignup::class, ['group' => $group])
        ->set('name', 'Anna Janssens')
        ->set('email', 'anna@gmail.com')
        ->set('roles', ['roze-hesje', 'dj'])
        ->set('message', 'Ik wil graag helpen.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $enquiry = ContactForm::firstWhere('email', 'anna@gmail.com');

    expect($enquiry)->not->toBeNull();
    expect($enquiry->message)
        ->toContain('Schaarbeek')   // routed to this chapter by context
        ->toContain('Roze hesje')
        ->toContain('DJ')
        ->toContain('Ik wil graag helpen.');

    Mail::assertSent(ContactFormSubmitted::class, fn ($mail) => $mail->hasTo('comms@example.com'));
});

it('requires a name and a valid email', function () {
    $group = Group::factory()->create();

    Livewire::test(ChapterVolunteerSignup::class, ['group' => $group])
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->call('submit')
        ->assertHasErrors(['name', 'email'])
        ->assertSet('submitted', false);
});

it('blocks a filled honeypot without creating an enquiry', function () {
    Mail::fake();
    $group = Group::factory()->create();

    Livewire::test(ChapterVolunteerSignup::class, ['group' => $group])
        ->set('name', 'Bot')
        ->set('email', 'bot@gmail.com')
        ->set('website', 'spam')
        ->call('submit')
        ->assertHasErrors(['website']);

    expect(ContactForm::count())->toBe(0);
    Mail::assertNothingSent();
});
