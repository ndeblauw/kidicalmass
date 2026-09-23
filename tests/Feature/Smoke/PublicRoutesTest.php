<?php

use App\Http\Middleware\SetLocale;

use function Pest\Laravel\get;

it('serves every public route with 200', function (string $path) {
    get($path)->assertOk();
})->with('public routes');

it('serves the core public pages in every supported locale', function (string $locale) {
    app()->setLocale($locale);

    $routes = [
        'home',
        'activities.index',
        'groups.index',
        'getting-started',
        'volunteer',
        'about',
        'about.mission',
        'about.vision',
        'about.organisation',
        'articles.index',
        'about.press',
        'about.partners',
        'membership',
        'contact',
        'privacy',
        'newsletter.show',
        'newsletter.confirmed',
    ];

    foreach ($routes as $name) {
        get(localized_route($name))->assertOk();
    }
})->with(SetLocale::SUPPORTED);
