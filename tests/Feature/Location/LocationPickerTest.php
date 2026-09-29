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

it('finds a place by any of its names, whatever the case or accents, and labels it in the current locale', function () {
    PostalCode::create([
        'zip' => '1000', 'name' => 'Brussel', 'name_nl' => 'Brussel', 'name_fr' => 'Bruxelles',
        'search_names' => PostalCode::searchNamesFor(['Brussel', 'Bruxelles']),
        'latitude' => 50.8504, 'longitude' => 4.3488,
    ]);
    PostalCode::create([
        'zip' => '4000', 'name' => 'Liège', 'name_nl' => 'Luik', 'name_fr' => 'Liège',
        'search_names' => PostalCode::searchNamesFor(['Luik', 'Liège', 'Glain', 'Rocourt']),
        'latitude' => 50.6337, 'longitude' => 5.5675,
    ]);

    Livewire::test(LocationPicker::class)
        ->set('query', 'brussel')
        ->assertSee('1000')
        ->assertDontSee('Bruxelles')
        ->set('query', 'LIEGE')
        ->assertSee('4000')
        ->assertSee('Luik')
        ->set('query', 'Rocourt')
        ->assertSee('Luik');

    app()->setLocale('fr');

    Livewire::test(LocationPicker::class)
        ->set('query', 'liège')
        ->assertSee('4000')
        ->assertSee('Liège')
        ->assertDontSee('Luik')
        ->set('query', 'Bruxelles')
        ->assertSee('1000');
});

it('treats LIKE wildcards in the query as plain characters', function () {
    foreach (['%%', '__', '1_9', '%e'] as $wildcard) {
        Livewire::test(LocationPicker::class)
            ->set('query', $wildcard)
            ->assertDontSee('1090')
            ->assertDontSee('9000');
    }
});

it('sets the location cookie and redirects when a zip is chosen', function () {
    Livewire::test(LocationPicker::class)
        ->call('choose', '1090')
        ->assertRedirect();

    expect(Cookie::hasQueued(config('location.cookie')))->toBeTrue();
});

it('lets a chosen location be changed in place and cancelled', function () {
    Livewire::test(LocationPicker::class, ['selected' => ['zip' => '9000', 'lat' => 51.0543, 'lng' => 3.7174, 'name' => 'Gent']])
        ->assertSeeHtml('data-state="chosen"')
        ->assertDontSeeHtml('id="location-picker-query"')
        ->set('editing', true)
        ->assertSeeHtml('data-state="editing"')
        ->assertSeeHtml('placeholder="Gent"')
        ->assertSee(__('common.location.cancel'))
        ->set('query', 'Jet')
        ->call('cancelEditing')
        ->assertSet('query', '')
        ->assertSeeHtml('data-state="chosen"');
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

it('clears the location and redirects in non-reactive mode', function () {
    Livewire::test(LocationPicker::class)
        ->call('clear')
        ->assertRedirect();

    $forgotten = Cookie::queued(config('location.cookie'));

    expect($forgotten)->not->toBeNull()
        ->and($forgotten->getExpiresTime())->toBeLessThan(time());
});

it('offers a clear link next to change when a location is set', function () {
    Livewire::test(LocationPicker::class)
        ->set('selected', ['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette'])
        ->assertSeeHtml('data-location-clear')
        ->assertSee(__('common.location.clear'));
});

it('clears the shown location in reactive mode', function () {
    Livewire::withCookie('kcm_location', json_encode([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]));

    Livewire::test(LocationPicker::class, ['reactive' => true])
        ->assertSeeHtml('data-location-clear')
        ->call('clear')
        ->assertDontSeeHtml('data-location-clear')
        ->assertSee(__('common.location.prompt'));
});

it('provides shared location picker copy in both locales', function () {
    $keys = [
        'common.location.current',
        'common.location.change',
        'common.location.cancel',
        'common.location.prompt',
        'common.location.placeholder',
        'common.location.suggestions_status',
        'common.location.suggestions_label',
        'common.location.clear',
        'common.location.clear_label',
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
        'common.location.suggestions_status' => '{1} Localized Location Suggestion|[2,*] Localized Location Suggestions',
        'common.location.suggestions_label' => 'Localized Location Suggestions Label',
        'common.location.current' => 'Localized Current Location',
        'common.location.change' => 'Localized Location Change',
        'common.location.cancel' => 'Localized Location Cancel',
        'common.location.clear' => 'Localized Location Clear',
        'common.location.clear_label' => 'Localized Location Clear Label',
    ];

    Lang::addLines($localizedCopy, 'fr');

    Livewire::test(LocationPicker::class)
        ->set('query', 'Jet')
        ->assertSee($localizedCopy['common.location.prompt'])
        ->assertSee($localizedCopy['common.location.placeholder'])
        ->assertSee('Localized Location Suggestion')
        ->assertSee($localizedCopy['common.location.suggestions_label']);

    Livewire::test(LocationPicker::class)
        ->set('selected', ['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette'])
        ->assertSee($localizedCopy['common.location.current'])
        ->assertSee('Jette')
        ->assertSee($localizedCopy['common.location.change'])
        ->assertSee($localizedCopy['common.location.clear'])
        ->assertSeeHtml($localizedCopy['common.location.clear_label'])
        ->set('editing', true)
        ->assertSee($localizedCopy['common.location.cancel']);
});
