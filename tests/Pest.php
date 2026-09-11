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
| Datasets
|--------------------------------------------------------------------------
*/

// Every no-parameter public (NL) page, shared by the public-route smoke test.
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
