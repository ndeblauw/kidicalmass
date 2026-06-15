<?php

use App\Livewire\StartGroupEnquiry;
use App\Mail\ContactFormSubmitted;
use App\Models\ContactForm;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

it('renders the start-a-group page with the deal, the honest asks and the intent form', function () {
    $this->get(route('groups.start'))
        ->assertOk()
        ->assertSee('Breng Kidical Mass naar jouw buurt')
        ->assertSee('Wat jij brengt')
        ->assertSee('Wat wij dragen')
        ->assertSee('Wat het écht vraagt')
        ->assertSee('Zin om te beginnen?');
});

it('submits a start-group enquiry tagged with place, path and motivation', function () {
    Mail::fake();

    Livewire::test(StartGroupEnquiry::class)
        ->set('name', 'Wim Janssens')
        ->set('email', 'wim@gmail.com')
        ->set('place', 'Mechelen')
        ->set('motivation', 'Mijn kinderen fietsen graag en de buurt mist het.')
        ->set('team', 'interesse')
        ->set('path', 'praten')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true)
        ->assertSet('confirmedPath', 'praten');

    $enquiry = ContactForm::firstWhere('email', 'wim@gmail.com');

    expect($enquiry)->not->toBeNull();
    expect($enquiry->message)
        ->toContain('Aanvraag nieuwe lokale groep')
        ->toContain('Mechelen')
        ->toContain('Ik praat eerst graag met iemand die het al deed')
        ->toContain('Een paar mensen tonen interesse')
        ->toContain('Mijn kinderen fietsen graag en de buurt mist het.');

    Mail::assertSent(ContactFormSubmitted::class);
});

it('requires name, email, place, motivation and a chosen path', function () {
    Livewire::test(StartGroupEnquiry::class)
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->set('place', '')
        ->set('motivation', '')
        ->set('path', '')
        ->call('submit')
        ->assertHasErrors(['name', 'email', 'place', 'motivation', 'path'])
        ->assertSet('submitted', false);
});

it('blocks a filled honeypot without creating an enquiry', function () {
    Mail::fake();

    Livewire::test(StartGroupEnquiry::class)
        ->set('name', 'Bot')
        ->set('email', 'bot@gmail.com')
        ->set('place', 'Spamstad')
        ->set('motivation', 'spam spam spam')
        ->set('path', 'klaar')
        ->set('website', 'spam')
        ->call('submit')
        ->assertHasErrors(['website']);

    expect(ContactForm::count())->toBe(0);
    Mail::assertNothingSent();
});
