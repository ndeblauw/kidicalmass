<?php

use Illuminate\Support\Facades\Blade;

use function Pest\Laravel\get;

it('renders a closing CTA heading and button on a yellow band', function () {
    $html = Blade::render(
        '<x-closing-cta heading="Klaar voor je eerste rit?" href="/events" label="Vind een rit" />'
    );

    expect($html)
        ->toContain('Klaar voor je eerste rit?')
        ->toContain('Vind een rit')
        ->toContain('bg-kidical-yellow')
        ->toContain('href="/events"');
});

dataset('pages with a closing CTA', [
    ['home', 'Klaar voor je eerste rit?'],
    ['activities.index', 'Zelf een rit in je buurt?'],
    ['getting-started', 'Klaar om mee te rijden?'],
    ['find-a-bike', 'Toch nog een vraag?'],
    ['volunteer', 'Geef de straat terug aan kinderen'],
    ['articles.index', 'Zin gekregen om mee te rijden?'],
    ['about.partners', 'Samen op pad?'],
    ['about.press', 'Vragen van de pers?'],
]);

it('renders the page-specific closing CTA', function (string $route, string $heading) {
    get(route($route))
        ->assertOk()
        ->assertSee($heading, escape: false);
})->with('pages with a closing CTA');
