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

it('renders the About hub with all six sub-page nav cards', function () {
    get('/nl/about')
        ->assertOk()
        ->assertDontSee('Stub', escape: false)
        ->assertSee('Over ons')
        // The 6 nav cards route to every leaf.
        ->assertSee(route('about.mission'), escape: false)
        ->assertSee(route('about.vision'), escape: false)
        ->assertSee(route('about.organisation'), escape: false)
        ->assertSee(route('articles.index'), escape: false)
        ->assertSee(route('about.press'), escape: false)
        ->assertSee(route('about.partners'), escape: false)
        // Tone of voice: no em-dashes in rendered copy.
        ->assertDontSee('—');
});

it('renders the Mission leaf with its key NL sections and forward links', function () {
    get('/nl/about/mission')
        ->assertOk()
        ->assertDontSee('Stub', escape: false)
        ->assertSee('Drie dingen die we doen')
        ->assertSee('Iedereen is welkom')
        ->assertSee('Doe mee met de beweging')
        // The corridor must hand the visitor forward.
        ->assertSee(route('getting-started'), escape: false)
        ->assertSee(route('about.vision'), escape: false)
        ->assertDontSee('—');
});

it('renders the Vision leaf with its four demands and parent voices', function () {
    get('/nl/about/vision')
        ->assertOk()
        ->assertDontSee('Stub', escape: false)
        ->assertSee('Wat we vragen')
        ->assertSee('Veilige fietsinfrastructuur voor kinderen en gezinnen')
        ->assertSee('het manifest')
        ->assertDontSee('—');
});

it('renders the Organisation leaf with the named coordination duo', function () {
    get('/nl/about/organisation')
        ->assertOk()
        ->assertDontSee('Stub', escape: false)
        ->assertSee('Wie wat doet')
        ->assertSee('Leticia')
        ->assertSee('Veiligheid en routes')
        ->assertSee(route('getting-started'), escape: false)
        ->assertDontSee('—');
});

it('renders the Partners leaf with curated partners and a contact CTA', function () {
    get('/nl/about/partners')
        ->assertOk()
        ->assertDontSee('Stub', escape: false)
        // Merged "who backs us": named institutional anchors + the breadth logo wall.
        ->assertSee('Onze partners en bondgenoten')
        ->assertSee('En vele anderen die Kidical Mass mee mogelijk maken')
        // In-kind partners fold into a one-line find-a-bike pointer (no dedicated cards).
        ->assertSee('Loopz')
        ->assertSee('bike@kidicalmass.be')
        ->assertSee(route('find-a-bike'), escape: false)
        // Honest: no faker/lorem partner names leak from the seeded model.
        ->assertDontSee('lorem')
        ->assertDontSee('—');
});

it('renders the Press leaf contact-forward with an honest empty state', function () {
    get('/nl/about/press')
        ->assertOk()
        ->assertDontSee('Stub', escape: false)
        ->assertSee('Journalisten, we praten graag')
        ->assertSee('bike@kidicalmass.be')
        ->assertSee('We bouwen aan een persoverzicht')
        ->assertDontSee('—');
});

it('renders the News feed in NL', function () {
    get('/nl/about/news')
        ->assertOk()
        ->assertDontSee('Stub', escape: false)
        ->assertSee('Nieuws')
        ->assertSee('Updates van de beweging')
        ->assertDontSee('—');
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

it('renders the Steun support page with its key NL sections', function () {
    get('/nl/steun-ons')
        ->assertOk()
        ->assertDontSee('Stub', escape: false)
        ->assertSee('Steun Kidical Mass')
        ->assertSee('Meefietsen is gratis voor elk gezin')
        ->assertSee('Steun vanaf €3 per maand')
        // The riding-stays-free reassurance is non-negotiable (and not phrased as a threat).
        ->assertSee('Meefietsen blijft altijd gratis')
        // Links out to Growfunding; the site processes no payments.
        ->assertSee('growfunding.be')
        // No backer count (dropped); movement scale instead.
        ->assertSee('honderden gezinnen')
        // Terminology: "lid" is retired; tone of voice: no em-dashes.
        ->assertDontSee('Word lid')
        ->assertDontSee('Lid worden')
        ->assertDontSee('—');
});

it('shows the support callout on the home page', function () {
    get('/nl')
        ->assertOk()
        ->assertSee('Kidical Mass blijft gratis')
        ->assertSee(route('membership'), escape: false);
});

it('shows the slim partner recognition strip site-wide, linking to the Partners page', function () {
    // Recognition only: funder credit + a single link to /about/partners.
    get('/nl')
        ->assertOk()
        ->assertSee('Mede mogelijk gemaakt door')
        ->assertSee('Met de steun van Brussel Mobiliteit')
        ->assertSee(route('about.partners'), escape: false)
        // Acquisition + the supporters list + the dead links moved OFF the global
        // strip onto /about/partners; they must not leak back into every page.
        ->assertDontSee('Ook partner worden?')
        ->assertDontSee('Sponsorformules')
        ->assertDontSee('Partnercharter')
        ->assertDontSee('Ook ondersteund door');
});

it('renders the Partners become-a-partner conversion flow (not a mailto black hole)', function () {
    get('/nl/about/partners')
        ->assertOk()
        // benefit hook + formules summary (the two tracks)
        ->assertSee('Waarom partner of sponsor worden?')
        ->assertSee('Onze formules')
        ->assertSee("Voor vzw's en verenigingen", escape: false)
        ->assertSee('Voor bedrijven')
        // depth = summary on-page + downloadable PDFs
        ->assertSee('downloads/kidical-mass-sponsorformules.pdf', escape: false)
        ->assertSee('downloads/kidical-mass-partnercharter.pdf', escape: false)
        // charter essence + the routed enquiry form (PAT-6), with email/phone fallback
        ->assertSee('Wat we van partners vragen')
        ->assertSee('Interesse? Laten we praten.')
        ->assertSee('Type organisatie')
        ->assertSee('Verstuur je aanvraag')
        ->assertSee('bike@kidicalmass.be')
        // tone of voice: no em-dashes in rendered copy
        ->assertDontSee('—');
});
