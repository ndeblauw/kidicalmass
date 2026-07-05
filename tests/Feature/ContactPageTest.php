<?php

use App\Livewire\ContactFormComponent;

use function Pest\Laravel\get;

// The page's structural invariants: the national form with its topic buckets,
// the direct address from config, and the volunteer redirect (volunteering
// routes via Help out, never through this page).
it('renders the contact page with the form, direct details and volunteer redirect', function () {
    get('/nl/contact')
        ->assertOk()
        ->assertSeeLivewire(ContactFormComponent::class)
        ->assertSee(config('kidicalmass.contact.email'))
        ->assertSee('tel:'.config('kidicalmass.contact.phone_e164'), false)
        ->assertSee(route('volunteer'), false);
});
