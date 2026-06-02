<?php

use function Pest\Laravel\get;

it('serves every no-parameter public route with 200', function (string $path) {
    get($path)->assertOk();
})->with([
    '/nl',
    '/nl/events',
    '/nl/chapters',
    '/nl/help-out',
    '/nl/getting-started',
    '/nl/about',
    '/nl/about/mission',
    '/nl/about/vision',
    '/nl/about/organisation',
    '/nl/about/news',
    '/nl/about/press',
    '/nl/about/partners',
    '/nl/membership',
    '/nl/contact',
    '/nl/privacy',
    '/nl/cookies',
]);

it('marks stub pages as unfinished', function () {
    get('/nl/getting-started')->assertSee('Stub', escape: false);
});
