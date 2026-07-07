<?php

use App\Livewire\PartnerEnquiry;
use App\Mail\ContactFormSubmitted;
use App\Models\ContactForm;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

it('submits a partner enquiry tagged with the organisation, type and formule', function () {
    Mail::fake();

    Livewire::test(PartnerEnquiry::class)
        ->set('name', 'Sofie Peeters')
        ->set('email', 'sofie@gmail.com')
        ->set('organisation', 'Fietswinkel De Ketting')
        ->set('type', 'bedrijf')
        ->set('formule', 'sponsor')
        ->set('message', 'We willen graag zichtbaar zijn op events.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $enquiry = ContactForm::firstWhere('email', 'sofie@gmail.com');

    expect($enquiry)->not->toBeNull();
    expect($enquiry->message)
        ->toContain('Aanvraag partnerschap')
        ->toContain('Fietswinkel De Ketting')
        ->toContain('Bedrijf')
        ->toContain('Sponsor')
        ->toContain('We willen graag zichtbaar zijn op events.');

    Mail::assertSent(ContactFormSubmitted::class);
});

it('requires name, email, organisation and type', function () {
    Livewire::test(PartnerEnquiry::class)
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->set('organisation', '')
        ->set('type', '')
        ->call('submit')
        ->assertHasErrors(['name', 'email', 'organisation', 'type'])
        ->assertSee(__('validation.custom.organisation.required'))
        ->assertSee(__('validation.custom.type.required'))
        ->assertSet('submitted', false);
});

it('blocks a filled honeypot without creating an enquiry', function () {
    Mail::fake();

    Livewire::test(PartnerEnquiry::class)
        ->set('name', 'Bot')
        ->set('email', 'bot@gmail.com')
        ->set('organisation', 'Spam BV')
        ->set('type', 'bedrijf')
        ->set('website', 'spam')
        ->call('submit')
        ->assertHasErrors(['website']);

    expect(ContactForm::count())->toBe(0);
    Mail::assertNothingSent();
});
