<?php

use App\Livewire\LocationPicker;
use App\Models\PostalCode;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Lang;
use Livewire\Livewire;

beforeEach(function () {
    PostalCode::create(['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265]);
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174]);
});

it('suggests postcodes by zip or name', function () {
    Livewire::test(LocationPicker::class)
        ->set('query', 'Jet')
        ->assertSee('1090')
        ->assertSee('Jette')
        ->assertDontSee('Gent');
});

it('sets the location cookie and redirects when a zip is chosen', function () {
    Livewire::test(LocationPicker::class)
        ->call('choose', '1090')
        ->assertRedirect();

    expect(Cookie::hasQueued(config('location.cookie')))->toBeTrue();
});

it('resolves the nearest postcode from geolocation coords', function () {
    Livewire::test(LocationPicker::class)
        ->call('setFromCoords', 50.88, 4.33)
        ->assertRedirect();

    expect(Cookie::hasQueued(config('location.cookie')))->toBeTrue();
});

it('dispatches location-selected and does not redirect in reactive mode', function () {
    Livewire::test(LocationPicker::class, ['reactive' => true])
        ->call('choose', '9000')
        ->assertDispatched('location-selected')
        ->assertNoRedirect();

    expect(Cookie::hasQueued(config('location.cookie')))->toBeTrue();
});

it('dispatches a null payload on clear in reactive mode without redirecting', function () {
    Livewire::test(LocationPicker::class, ['reactive' => true])
        ->call('clear')
        ->assertDispatched('location-selected')
        ->assertNoRedirect();
});

it('provides shared location picker copy in both locales', function () {
    $keys = [
        'common.location.current',
        'common.location.change',
        'common.location.prompt',
        'common.location.placeholder',
        'common.location.locate',
        'common.location.locating',
        'common.location.error',
        'common.location.suggestions_status',
        'common.location.suggestions_label',
    ];

    foreach (['nl', 'fr'] as $locale) {
        foreach ($keys as $key) {
            expect(__($key, [], $locale))->not->toBe($key);
        }
    }
});

it('renders localized location picker copy across its states', function () {
    app()->setLocale('fr');

    $localizedCopy = [
        'common.location.prompt' => 'Localized Location Prompt',
        'common.location.placeholder' => 'Localized Location Placeholder',
        'common.location.locate' => 'Localized Location Locate',
        'common.location.locating' => 'Localized Location Locating',
        'common.location.error' => 'Localized Location Error',
        'common.location.suggestions_status' => '{1} Localized Location Suggestion|[2,*] Localized Location Suggestions',
        'common.location.suggestions_label' => 'Localized Location Suggestions Label',
        'common.location.current' => 'Localized Current Location',
        'common.location.change' => 'Localized Location Change',
    ];

    Lang::addLines($localizedCopy, 'fr');

    Livewire::test(LocationPicker::class)
        ->set('query', 'Jet')
        ->assertSee($localizedCopy['common.location.prompt'])
        ->assertSee($localizedCopy['common.location.placeholder'])
        ->assertSee($localizedCopy['common.location.locate'])
        ->assertSee($localizedCopy['common.location.locating'])
        ->assertSee($localizedCopy['common.location.error'])
        ->assertSee('Localized Location Suggestion')
        ->assertSee($localizedCopy['common.location.suggestions_label']);

    Livewire::test(LocationPicker::class)
        ->set('selected', ['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette'])
        ->assertSee($localizedCopy['common.location.current'])
        ->assertSee('Jette')
        ->assertSee($localizedCopy['common.location.change']);
});
