<?php

use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use Illuminate\Support\Facades\Lang;

use function Pest\Laravel\get;

beforeEach(function (): void {
    $this->withoutVite();
});

it('redirects the bare root to the nl prefix', function () {
    get('/')->assertRedirect('/nl');
});

it('serves the home page under /nl with a nl lang attribute', function () {
    get('/nl')
        ->assertOk()
        ->assertSee('lang="nl"', escape: false);
});

it('serves the home page under /fr with a fr lang attribute', function () {
    get('/fr')
        ->assertOk()
        ->assertSee('lang="fr"', escape: false);
});

it('keeps Dutch route names and exposes French route names', function () {
    expect(route('activities.index', ['locale' => 'nl']))->toEndWith('/nl/events');
    expect(route('fr.activities.index'))->toEndWith('/fr/agenda');
});

it('generates a route for the active locale', function () {
    app()->setLocale('fr');

    expect(localized_route('activities.index'))->toEndWith('/fr/agenda');
});

it('generates clean static routes for the active locale', function () {
    app()->setLocale('fr');

    expect(localized_route('home'))->toEndWith('/fr');
    expect(localized_route('contact'))->toEndWith('/fr/contact');
});

it('does not serve French slugs under the Dutch locale', function () {
    get('/nl/agenda')->assertNotFound();
});

it('does not serve Dutch slugs under the French locale', function () {
    get('/fr/events')->assertNotFound();
});

it('shows a missing translation marker in staging when configured', function () {
    app()->detectEnvironment(fn (): string => 'staging');
    config(['i18n.show_missing_translation_keys' => true]);

    expect(__('locale_layer.missing', [], 'fr'))->toBe('[[locale_layer.missing]]');
});

it('silently falls back to Dutch translations in production', function () {
    app()->detectEnvironment(fn (): string => 'production');
    config(['i18n.show_missing_translation_keys' => true]);
    Lang::addLines(['locale_layer.fallback' => 'fallback-value'], 'nl');

    expect(__('locale_layer.fallback', [], 'fr'))->toBe('fallback-value');
});

it('shows a marker for a French key that would otherwise fall back in staging', function () {
    app()->detectEnvironment(fn (): string => 'staging');
    config(['i18n.show_missing_translation_keys' => true]);
    Lang::addLines(['locale_layer.fallback' => 'fallback-value'], 'nl');

    expect(__('locale_layer.fallback', [], 'fr'))->toBe('[[locale_layer.fallback]]');
});

it('silently falls back to Dutch translations when the staging marker is disabled', function () {
    app()->detectEnvironment(fn (): string => 'staging');
    config(['i18n.show_missing_translation_keys' => false]);
    Lang::addLines(['locale_layer.fallback' => 'fallback-value'], 'nl');

    expect(__('locale_layer.fallback', [], 'fr'))->toBe('fallback-value');
});

it('dispatches French detail routes with the locale before their models', function () {
    $activity = Activity::factory()->create();
    $group = Group::factory()->create();
    $article = Article::factory()->create();

    get('/fr/agenda/'.$activity->getRouteKey())->assertOk();
    get('/fr/agenda/'.$activity->getRouteKey().'/ical')->assertOk();
    get('/fr/groupes-locaux/'.$group->getRouteKey())->assertOk();
    get('/fr/a-propos/actualites/'.$article->getRouteKey())->assertOk();
});

it('uses the active locale in the build layout', function () {
    app()->setLocale('fr');

    expect(view('layouts.build', ['slot' => ''])->render())->toContain('lang="fr"');
});

it('redirects a legacy path to the French route for an explicit French preference', function () {
    get('/agenda', ['Accept-Language' => 'fr-BE,fr;q=0.9,nl;q=0.8'])
        ->assertMovedPermanently()
        ->assertRedirect('/fr/agenda');
});

it('serves the renamed IA paths for existing pages', function () {
    $group = Group::factory()->create();
    $activity = Activity::factory()->create(['begin_date' => now()->addWeek()]);
    $activity->groups()->attach($group);
    Article::factory()->create();

    get('/nl/events')->assertOk();
    get('/nl/chapters')->assertOk();
    get('/nl/about/news')->assertOk();
    get('/nl/help-out')->assertOk();
});
