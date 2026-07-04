<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/*
|--------------------------------------------------------------------------
| Datasets
|--------------------------------------------------------------------------
*/

// Every no-parameter public (NL) page. The route list lives here once and feeds
// both the 200-smoke test and the tone-of-voice guard.
$publicRoutes = [
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
    '/nl/steun-ons',
    '/nl/contact',
    '/nl/privacy',
    '/nl/nieuwsbrief',
    '/nl/nieuwsbrief/bevestigd',
];

dataset('public routes', $publicRoutes);

// Pages still served by the <x-stub> placeholder are intentionally unfinished and
// carry the "Stub —" banner, so the finished-page tone guard skips them. Drop a
// route from this list once its real content lands.
$stubRoutes = ['/nl/contact', '/nl/privacy'];

dataset('finished public routes', array_values(array_diff($publicRoutes, $stubRoutes)));
