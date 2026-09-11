<?php

use App\Livewire\NewsletterSignup;
use App\Models\Group;
use App\Models\PostalCode;
use App\Models\User;
use Livewire\Livewire;

it('rejects an invalid email and stays on the form', function () {
    Livewire::test(NewsletterSignup::class)
        ->set('email', 'not-an-email')
        ->call('subscribe')
        ->assertHasErrors(['email' => 'email'])
        ->assertSet('submitted', false);
});

it('shows a clear Dutch message when the email is left empty', function () {
    Livewire::test(NewsletterSignup::class)
        ->set('email', '')
        ->call('subscribe')
        ->assertHasErrors(['email' => 'required'])
        ->assertSee('Vul je e-mailadres in')
        ->assertDontSee('validation.required');
});

it('validates the email on blur, before submit, but stays quiet while empty', function () {
    // Leaving an empty field should not nag yet.
    Livewire::test(NewsletterSignup::class)
        ->set('email', '')
        ->assertHasNoErrors('email');

    // Leaving a malformed field flags it immediately, without calling subscribe.
    Livewire::test(NewsletterSignup::class)
        ->set('email', 'nope')
        ->assertHasErrors(['email' => 'email'])
        ->assertSet('submitted', false);
});

it('trims and lowercases the email before subscribing', function () {
    Livewire::test(NewsletterSignup::class)
        ->set('email', '  Ouders@Example.BE  ')
        ->call('subscribe')
        ->assertHasNoErrors()
        ->assertSet('email', 'ouders@example.be')
        ->assertSet('submitted', true);
});

it('rejects an absurdly long email', function () {
    $longEmail = str_repeat('a', 250).'@example.be';

    Livewire::test(NewsletterSignup::class)
        ->set('email', $longEmail)
        ->call('subscribe')
        ->assertHasErrors(['email' => 'max'])
        ->assertSet('submitted', false);
});

it('lets a cold visitor subscribe with email only, groups collapsed', function () {
    Livewire::test(NewsletterSignup::class)
        ->assertSet('showGroups', false)
        ->assertSee('Ritten bij jou in de buurt kiezen')
        ->assertDontSee('Bekijk alle groepen')
        ->set('email', 'ouders@example.be')
        ->call('subscribe')
        ->assertSet('submitted', true)
        ->assertSee('Kijk even in je mailbox');
});

it('reveals the picker when the disclosure link is clicked', function () {
    Livewire::test(NewsletterSignup::class)
        ->call('revealGroups')
        ->assertSet('showGroups', true);
});

it('populates and pre-selects nearby groups when a location is picked, keeping the email', function () {
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174]);
    $group = Group::create([
        'shortname' => 'gent',
        'name' => 'Kidical Mass Gent',
        'zip' => '9000',
        'invisible' => false,
        'started_at' => now(),
    ]);

    Livewire::test(NewsletterSignup::class)
        ->set('email', 'ouders@example.be')
        ->dispatch('location-selected', payload: ['zip' => '9000', 'lat' => 51.0543, 'lng' => 3.7174, 'name' => 'Gent'])
        ->assertSet('showGroups', true)
        ->assertSet('selectedGroups', [$group->id])
        ->assertSet('email', 'ouders@example.be')
        ->assertSee('We sturen je standaard de ritten van deze groepen');
});

it('blocks submit when nearby groups are shown but all are deselected', function () {
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174]);
    Group::create([
        'shortname' => 'gent',
        'name' => 'Kidical Mass Gent',
        'zip' => '9000',
        'invisible' => false,
        'started_at' => now(),
    ]);

    Livewire::test(NewsletterSignup::class)
        ->set('email', 'ouders@example.be')
        ->dispatch('location-selected', payload: ['zip' => '9000', 'lat' => 51.0543, 'lng' => 3.7174, 'name' => 'Gent'])
        ->set('selectedGroups', [])
        ->call('subscribe')
        ->assertHasErrors('selectedGroups')
        ->assertSet('submitted', false);
});

it('follows every visible chapter when "Heel België" is ticked, overriding the nearby picks', function () {
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174]);
    $gent = Group::create([
        'shortname' => 'gent',
        'name' => 'Kidical Mass Gent',
        'zip' => '9000',
        'invisible' => false,
        'started_at' => now(),
    ]);
    // A visible chapter with no nearby postcode: it never appears in the chips,
    // so "Heel België" is the only way to include it.
    $far = Group::create([
        'shortname' => 'antwerpen',
        'name' => 'Kidical Mass Antwerpen',
        'zip' => '2000',
        'invisible' => false,
        'started_at' => now(),
    ]);

    $component = Livewire::test(NewsletterSignup::class)
        ->set('email', 'ouders@example.be')
        ->dispatch('location-selected', payload: ['zip' => '9000', 'lat' => 51.0543, 'lng' => 3.7174, 'name' => 'Gent'])
        ->assertSet('selectedGroups', [$gent->id])
        ->set('followAll', true);

    expect($component->instance()->resolvedGroupIds())->toBe([$gent->id, $far->id]);

    $component->call('subscribe')->assertSet('submitted', true);
});

it('resolves to the nearby picks when "Heel België" is not ticked', function () {
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174]);
    $gent = Group::create([
        'shortname' => 'gent',
        'name' => 'Kidical Mass Gent',
        'zip' => '9000',
        'invisible' => false,
        'started_at' => now(),
    ]);
    Group::create([
        'shortname' => 'antwerpen',
        'name' => 'Kidical Mass Antwerpen',
        'zip' => '2000',
        'invisible' => false,
        'started_at' => now(),
    ]);

    $component = Livewire::test(NewsletterSignup::class)
        ->dispatch('location-selected', payload: ['zip' => '9000', 'lat' => 51.0543, 'lng' => 3.7174, 'name' => 'Gent']);

    expect($component->instance()->resolvedGroupIds())->toBe([$gent->id]);
});

it('lets a visitor subscribe with "Heel België" even when no nearby chip is kept', function () {
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174]);
    Group::create([
        'shortname' => 'gent',
        'name' => 'Kidical Mass Gent',
        'zip' => '9000',
        'invisible' => false,
        'started_at' => now(),
    ]);
    Group::create([
        'shortname' => 'antwerpen',
        'name' => 'Kidical Mass Antwerpen',
        'zip' => '2000',
        'invisible' => false,
        'started_at' => now(),
    ]);

    Livewire::test(NewsletterSignup::class)
        ->set('email', 'ouders@example.be')
        ->dispatch('location-selected', payload: ['zip' => '9000', 'lat' => 51.0543, 'lng' => 3.7174, 'name' => 'Gent'])
        ->set('selectedGroups', [])
        ->set('followAll', true)
        ->call('subscribe')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);
});

it('offers "Heel België" when chapters exist beyond the nearby ones', function () {
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174]);
    Group::create([
        'shortname' => 'gent',
        'name' => 'Kidical Mass Gent',
        'zip' => '9000',
        'invisible' => false,
        'started_at' => now(),
    ]);
    Group::create([
        'shortname' => 'antwerpen',
        'name' => 'Kidical Mass Antwerpen',
        'zip' => '2000',
        'invisible' => false,
        'started_at' => now(),
    ]);

    Livewire::test(NewsletterSignup::class)
        ->dispatch('location-selected', payload: ['zip' => '9000', 'lat' => 51.0543, 'lng' => 3.7174, 'name' => 'Gent'])
        ->assertSee('Heel België');
});

it('hides "Heel België" when every chapter is already shown nearby', function () {
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174]);
    Group::create([
        'shortname' => 'gent',
        'name' => 'Kidical Mass Gent',
        'zip' => '9000',
        'invisible' => false,
        'started_at' => now(),
    ]);

    Livewire::test(NewsletterSignup::class)
        ->dispatch('location-selected', payload: ['zip' => '9000', 'lat' => 51.0543, 'lng' => 3.7174, 'name' => 'Gent'])
        ->assertDontSee('Heel België');
});

it('does not require a group when revealed without any nearby chapters', function () {
    Livewire::test(NewsletterSignup::class)
        ->call('revealGroups')
        ->set('email', 'ouders@example.be')
        ->call('subscribe')
        ->assertSet('submitted', true);
});

it('greets a logged-in visitor instead of showing the form', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(NewsletterSignup::class)
        ->assertSee('Je bent al mee')
        ->assertDontSee('Je e-mailadres');
});
