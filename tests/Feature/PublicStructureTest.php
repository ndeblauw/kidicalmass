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
    '/nl/find-a-bike',
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
]);

it('marks stub pages as unfinished', function () {
    // About leaves are still stubs; Getting Started has been built out.
    get('/nl/about/mission')->assertSee('Stub', escape: false);
});

it('renders the Getting Started page with its key NL sections', function () {
    get('/nl/getting-started')
        ->assertOk()
        ->assertDontSee('Stub', escape: false)
        ->assertSee('Wat je mag verwachten op een rit')
        ->assertSee('Veelgestelde vragen')
        ->assertSee('Klaar voor je eerste rit?', escape: false)
        // Distilled 2026-06-02: "other ways to cycle" relocated off this page.
        ->assertDontSee('Andere manieren')
        // Safety reassurance sourced from the volunteer ROI + Jorge interview.
        ->assertSee('Is het veilig in het verkeer?')
        ->assertSee('lokale politie')
        // No-bike detail moved to its own page (2026-06-02); FAQ folds + links out.
        ->assertSee('Wat als we geen fiets hebben?')
        ->assertSee(route('find-a-bike'), escape: false)
        ->assertDontSee('KIDICALMASS');
});

it('renders the find-a-bike resource page with the providers', function () {
    get('/nl/find-a-bike')
        ->assertOk()
        ->assertSee('Geen fiets? Geen probleem', escape: false)
        ->assertSee('Kidical Mouse')
        ->assertSee('KIDICALMASS')
        ->assertSee('€30/jaar', escape: false)
        ->assertSee('Cyclo')
        // Resource content only — none of the first-ride FAQ leaks onto it.
        ->assertDontSee('Is het veilig in het verkeer?');
});

it('renders the Help out orientation page with its key NL sections', function () {
    get('/nl/help-out')
        ->assertOk()
        ->assertDontSee('Meer info volgt') // old stub gone
        ->assertSee('Wat meedoen inhoudt')
        ->assertSee('Hoe je kan helpen')
        ->assertSee('Roze hesje')
        // Term: "lokale groep" (the coordination duo's NL word for a chapter).
        ->assertSee('Vind je lokale groep')
        // Reframe: orientation page that ROUTES to the chapter; the contact form lives
        // there, not here. The single primary action points at the chapters index.
        ->assertSee(route('groups.index'), escape: false)
        // Tone of voice: no em-dashes in rendered copy.
        ->assertDontSee('—');
});

it('routes the home "New here?" entry link to Getting Started', function () {
    get('/nl')
        ->assertOk()
        ->assertSee(route('getting-started'), escape: false)
        ->assertSee('New here? Start here', escape: false);
});
