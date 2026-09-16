<?php

use App\Livewire\NewsletterSignup;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

function configureMailerLite(): void
{
    config([
        'services.mailerlite.token' => 'test-token',
        'services.mailerlite.base_url' => 'https://connect.mailerlite.com/api',
        'services.mailerlite.groups.nl' => 'group-nl',
        'services.mailerlite.groups.fr' => 'group-fr',
    ]);
}

function fakeMailerLiteSubscription(): void
{
    Http::preventStrayRequests();
    Http::fake([
        'https://connect.mailerlite.com/api/subscribers' => Http::response([
            'data' => ['id' => 'subscriber-123'],
        ], 201),
    ]);
}

it('subscribes a Dutch visitor to the Dutch MailerLite group', function () {
    configureMailerLite();
    fakeMailerLiteSubscription();

    Livewire::test(NewsletterSignup::class)
        ->set('email', '  Ouders@Example.BE  ')
        ->call('subscribe')
        ->assertHasNoErrors()
        ->assertSet('email', 'ouders@example.be')
        ->assertSet('submitted', true)
        ->assertSee('Kijk even in je mailbox');

    Http::assertSent(function (Request $request): bool {
        return $request->method() === 'POST'
            && $request->url() === 'https://connect.mailerlite.com/api/subscribers'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->data() === [
                'email' => 'ouders@example.be',
                'groups' => ['group-nl'],
            ];
    });

});

it('subscribes a French visitor to the French MailerLite group', function () {
    app()->setLocale('fr');
    configureMailerLite();
    fakeMailerLiteSubscription();

    Livewire::test(NewsletterSignup::class)
        ->set('email', 'ouders@example.be')
        ->call('subscribe')
        ->assertSet('submitted', true);

    Http::assertSent(function (Request $request): bool {
        return $request->method() === 'POST'
            && $request->url() === 'https://connect.mailerlite.com/api/subscribers'
            && $request->data() === [
                'email' => 'ouders@example.be',
                'groups' => ['group-fr'],
            ];
    });

});

it('leaves an existing opposite language group untouched', function () {
    configureMailerLite();
    Http::preventStrayRequests();
    Http::fake([
        'https://connect.mailerlite.com/api/subscribers' => Http::response([
            'data' => ['id' => 'subscriber-123'],
        ], 201),
    ]);

    Livewire::test(NewsletterSignup::class)
        ->set('email', 'ouders@example.be')
        ->call('subscribe')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    Http::assertSentCount(1);
});

it('keeps the form open and shows a friendly message when MailerLite fails', function () {
    configureMailerLite();
    Http::preventStrayRequests();
    Http::fake([
        'https://connect.mailerlite.com/api/subscribers' => Http::response([
            'message' => 'Service unavailable',
        ], 503),
    ]);

    Livewire::test(NewsletterSignup::class)
        ->set('email', 'ouders@example.be')
        ->call('subscribe')
        ->assertHasErrors(['email'])
        ->assertSee('Er ging iets mis bij het inschrijven. Probeer het later opnieuw.')
        ->assertSet('submitted', false);
});

it('rejects an invalid email without calling MailerLite', function () {
    configureMailerLite();
    Http::preventStrayRequests();

    Livewire::test(NewsletterSignup::class)
        ->set('email', 'not-an-email')
        ->call('subscribe')
        ->assertHasErrors(['email' => 'email'])
        ->assertSet('submitted', false);
});

it('rejects an absurdly long email without calling MailerLite', function () {
    configureMailerLite();
    Http::preventStrayRequests();

    Livewire::test(NewsletterSignup::class)
        ->set('email', str_repeat('a', 250).'@example.be')
        ->call('subscribe')
        ->assertHasErrors(['email' => 'max'])
        ->assertSet('submitted', false);
});

it('shows a clear Dutch message when the email is left empty', function () {
    configureMailerLite();
    Http::preventStrayRequests();

    Livewire::test(NewsletterSignup::class)
        ->set('email', '')
        ->call('subscribe')
        ->assertHasErrors(['email' => 'required'])
        ->assertSee('Vul je e-mailadres in')
        ->assertDontSee('validation.required');
});

it('validates the email on blur, before submit, but stays quiet while empty', function () {
    Livewire::test(NewsletterSignup::class)
        ->set('email', '')
        ->assertHasNoErrors('email');

    Livewire::test(NewsletterSignup::class)
        ->set('email', 'nope')
        ->assertHasErrors(['email' => 'email'])
        ->assertSet('submitted', false);
});

it('does not show the retired local-rides selection in the signup form', function () {
    Livewire::test(NewsletterSignup::class)
        ->assertDontSee('Ritten bij jou in de buurt kiezen')
        ->assertDontSee('location-picker')
        ->assertDontSee('We sturen je standaard de ritten van deze groepen');
});

it('greets a logged-in visitor instead of showing the form', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(NewsletterSignup::class)
        ->assertSee('Je bent al mee')
        ->assertDontSee('Je e-mailadres');
});
