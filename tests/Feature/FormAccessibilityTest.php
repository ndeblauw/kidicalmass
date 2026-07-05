<?php

use App\Livewire\ChapterVolunteerSignup;
use App\Livewire\NewsletterSignup;
use App\Livewire\PartnerEnquiry;
use App\Livewire\StartGroupEnquiry;
use App\Models\Group;
use Livewire\Livewire;

it('associates and announces validation errors on the chapter volunteer form', function () {
    $group = Group::factory()->create();

    Livewire::test(ChapterVolunteerSignup::class, ['group' => $group])
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->call('submit')
        ->assertSeeHtml('aria-invalid="true"')
        ->assertSeeHtml('aria-describedby="chapter-volunteer-email-error"')
        ->assertSeeHtml('id="chapter-volunteer-email-error"')
        ->assertSeeHtml('role="alert"');
});

it('associates and announces validation errors on the partner enquiry form', function () {
    Livewire::test(PartnerEnquiry::class)
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->call('submit')
        ->assertSeeHtml('aria-invalid="true"')
        ->assertSeeHtml('aria-describedby="partner-email-error"')
        ->assertSeeHtml('id="partner-email-error"')
        ->assertSeeHtml('role="alert"');
});

it('associates and announces validation errors on the start-a-group form', function () {
    Livewire::test(StartGroupEnquiry::class)
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->call('submit')
        ->assertSeeHtml('aria-invalid="true"')
        ->assertSeeHtml('aria-describedby="sg-email-error"')
        ->assertSeeHtml('id="sg-email-error"')
        ->assertSeeHtml('role="alert"');
});

it('announces enquiry success as a status message', function (string $component) {
    $params = $component === ChapterVolunteerSignup::class
        ? ['group' => Group::factory()->create()]
        : [];

    Livewire::test($component, $params)
        ->set('submitted', true)
        ->assertSeeHtml('role="status"');
})->with([
    ChapterVolunteerSignup::class,
    PartnerEnquiry::class,
    StartGroupEnquiry::class,
]);

it('announces the sending state while an enquiry submits', function (string $component) {
    $params = $component === ChapterVolunteerSignup::class
        ? ['group' => Group::factory()->create()]
        : [];

    Livewire::test($component, $params)
        ->assertSeeHtml('role="status"')
        ->assertSee('Bezig met versturen');
})->with([
    ChapterVolunteerSignup::class,
    PartnerEnquiry::class,
    StartGroupEnquiry::class,
]);

it('marks the newsletter email as required and announces success as a status message', function () {
    Livewire::test(NewsletterSignup::class)
        ->assertSeeHtml('required');

    Livewire::test(NewsletterSignup::class)
        ->set('email', 'ouders@example.be')
        ->call('subscribe')
        ->assertSet('submitted', true)
        ->assertSeeHtml('role="status"');
});
