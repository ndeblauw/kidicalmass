<?php

use App\Livewire\LocationPicker;
use App\Models\PostalCode;
use Illuminate\Support\Facades\Cookie;
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

it('exposes combobox markup so suggestions are keyboard navigable', function () {
    Livewire::test(LocationPicker::class)
        ->set('query', 'Jet')
        ->assertSeeHtml('role="combobox"')
        ->assertSeeHtml('aria-controls="location-picker-suggestions"')
        ->assertSeeHtml('role="listbox"')
        ->assertSeeHtml('role="option"')
        ->assertSeeHtml('data-option');
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

it('still redirects on choose in default (non-reactive) mode', function () {
    Livewire::test(LocationPicker::class)
        ->call('choose', '1090')
        ->assertRedirect();
});
