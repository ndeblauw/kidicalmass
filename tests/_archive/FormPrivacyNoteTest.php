<?php

use App\Livewire\ChapterVolunteerSignup;
use App\Livewire\ContactFormComponent;
use App\Livewire\NewsletterSignup;
use App\Livewire\PartnerEnquiry;
use App\Livewire\StartGroupEnquiry;
use App\Models\Group;
use Livewire\Livewire;

it('shows a privacy note linking the privacy page on every enquiry form', function (string $component) {
    Livewire::test($component)
        ->assertSee('privacyverklaring')
        ->assertSee(route('privacy', ['locale' => app()->getLocale()]), false);
})->with([
    'contact' => ContactFormComponent::class,
    'partner' => PartnerEnquiry::class,
    'start a group' => StartGroupEnquiry::class,
    'newsletter' => NewsletterSignup::class,
]);

it('shows a privacy note on the chapter volunteer form', function () {
    Livewire::test(ChapterVolunteerSignup::class, ['group' => Group::factory()->create()])
        ->assertSee('privacyverklaring')
        ->assertSee(route('privacy', ['locale' => app()->getLocale()]), false);
});
