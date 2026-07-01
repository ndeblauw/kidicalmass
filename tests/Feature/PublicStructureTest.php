<?php

use App\Models\Activity;
use App\Models\Group;

use function Pest\Laravel\get;

it('serves every no-parameter public route with 200', function (string $path) {
    get($path)->assertOk();
})->with('public routes');

it('renders the event detail page', function () {
    $activity = Activity::factory()->create();

    get(route('activities.show', ['locale' => 'nl', 'activity' => $activity]))->assertOk();
});

it('renders the chapter detail page', function () {
    $group = Group::factory()->create();

    get(route('groups.show', ['locale' => 'nl', 'group' => $group]))->assertOk();
});

// Honesty guard for every finished public page: no leftover "Stub" scaffolding
// (a stub route miswired as done) and no faker "lorem" leaking from seeds.
it('keeps stub scaffolding and lorem placeholder off every finished page', function (string $path) {
    get($path)
        ->assertOk()
        ->assertDontSee('Stub', escape: false)
        ->assertDontSee('lorem');
})->with('finished public routes');

it('renders the About hub: six nav cards plus an intention strip to the right exits', function () {
    get('/nl/about')
        ->assertOk()
        ->assertSee('Over ons')
        // The 6 nav cards route to every leaf.
        ->assertSee(route('about.mission'), escape: false)
        ->assertSee(route('about.vision'), escape: false)
        ->assertSee(route('about.organisation'), escape: false)
        ->assertSee(route('articles.index'), escape: false)
        ->assertSee(route('about.press'), escape: false)
        ->assertSee(route('about.partners'), escape: false)
        // The intention strip triages deciders to actions, not just more reading.
        ->assertSee('Waar ben je naar op zoek?')
        ->assertSee('Of lees meer over de beweging')
        ->assertSee(route('volunteer'), escape: false)
        ->assertSee(route('membership'), escape: false);
});

it('renders the Mission leaf with its key NL sections, forward links and the peak-intent Steun ask', function () {
    get('/nl/about/mission')
        ->assertOk()
        ->assertSee('Drie dingen die we doen')
        ->assertSee('Iedereen is welkom')
        ->assertSee('Samen maken we straten veiliger')
        // The corridor must hand the visitor forward.
        ->assertSee(route('getting-started'), escape: false)
        ->assertSee(route('about.vision'), escape: false)
        // Post-stats peak-intent moment: the free-rides line + Steun/Help exits.
        ->assertSee('Al onze ritten zijn gratis')
        ->assertSee(route('membership'), escape: false)
        ->assertSee(route('volunteer'), escape: false);
});

it('renders the Vision leaf with its four demands, parent voices and closing actions', function () {
    get('/nl/about/vision')
        ->assertOk()
        ->assertSee('Wat we vragen')
        ->assertSee('Veilige fietsinfrastructuur voor kinderen en gezinnen')
        ->assertSee('het manifest')
        // Closing actions: Help mee + Steun.
        ->assertSee(route('volunteer'), escape: false)
        ->assertSee(route('membership'), escape: false);
});

it('renders the Organisation leaf with the named coordination duo and the no-paid-staff Steun ask', function () {
    get('/nl/about/organisation')
        ->assertOk()
        ->assertSee('Wie wat doet')
        ->assertSee('Leticia')
        ->assertSee('Veiligheid en routes')
        ->assertSee(route('getting-started'), escape: false)
        // Steun ask lands right after the money model.
        ->assertSee('Geen hoofdkantoor, geen betaald personeel')
        ->assertSee(route('membership'), escape: false);
});

it('renders the Partners leaf with curated partners and a contact CTA', function () {
    get('/nl/about/partners')
        ->assertOk()
        // Merged "who backs us": named institutional anchors + the breadth logo wall.
        ->assertSee('Onze partners en bondgenoten')
        ->assertSee('En vele anderen die Kidical Mass mee mogelijk maken')
        // In-kind partners fold into a one-line find-a-bike pointer (no dedicated cards).
        ->assertSee('Loopz')
        ->assertSee('bike@kidicalmass.be')
        ->assertSee(route('find-a-bike'), escape: false);
});

it('renders the Press leaf contact-forward with an honest empty state', function () {
    get('/nl/about/press')
        ->assertOk()
        ->assertSee('Journalisten, we praten graag')
        ->assertSee('bike@kidicalmass.be')
        ->assertSee('We bouwen aan een persoverzicht');
});

it('renders the News feed in NL', function () {
    get('/nl/about/news')
        ->assertOk()
        ->assertSee('Nieuws')
        ->assertSee('Updates van de beweging');
});

it('renders the Getting Started page with its key NL sections', function () {
    get('/nl/getting-started')
        ->assertOk()
        ->assertSee('Wat je mag verwachten op een rit')
        ->assertSee('Veelgestelde vragen')
        ->assertSee('Klaar om mee te rijden?', escape: false)
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
        // The "wat meedoen inhoudt" eyebrow was dropped (obsolete); the two scrollytelling
        // blocks now lead the section.
        ->assertSee('Wat je krijgt')
        ->assertSee('Wat we vragen')
        ->assertSee('Hoe je kan helpen')
        ->assertSee('Roze hesje')
        // Term: "lokale groep" (the coordination duo's NL word for a chapter).
        ->assertSee('Vind je lokale groep')
        // Coda for buurts without a chapter: the single action funnels to "start a group".
        ->assertSee('Nog geen lokale groep in je buurt?')
        ->assertSee(route('groups.start'), escape: false);
});

it('routes the home "New here?" entry link to Getting Started', function () {
    get('/nl')
        ->assertOk()
        ->assertSee(route('getting-started'), escape: false)
        ->assertSee('Zo werkt een rit', escape: false);
});

it('renders the Steun support page with its key NL sections', function () {
    get('/nl/steun-ons')
        ->assertOk()
        ->assertSee('Steun Kidical Mass')
        // Mission-led hero: the cause leads the ask.
        ->assertSee('Samen maken we straten veilig voor kinderen.')
        // Proof + load share one story section; the stat deck backs it with live
        // numbers (the always-present local-groups card; values asserted in
        // SupportStatsTest / SteunOnsPageTest, which seed the data).
        ->assertSee('Een grote beweging op de schouders van een klein team')
        ->assertSee(__('support.stat_groups'))
        ->assertSee('Steun vanaf €3 per maand')
        // Links out to Growfunding; the site processes no payments.
        ->assertSee('growfunding.be')
        // The single closing ask reassures that riding stays free.
        ->assertSee('meefietsen blijft altijd gratis')
        // Terminology: "lid" is retired.
        ->assertDontSee('Word lid')
        ->assertDontSee('Lid worden');
});

it('keeps the support callout off the home page', function () {
    // Steun is already front-and-centre (persistent nav "Steun ons" + the closing CTA),
    // so the mid-flow support band is intentionally absent here. The callout component
    // still lives on other pages; it just doesn't interrupt the home dispatcher →
    // closing-CTA flow.
    get('/nl')
        ->assertOk()
        ->assertDontSee('Kidical Mass blijft gratis');
});

it('shows the slim partner recognition strip site-wide, linking to the Partners page', function () {
    // Recognition only: funder credit + a single link to /about/partners.
    get('/nl')
        ->assertOk()
        ->assertSee('Mede mogelijk gemaakt door Brussel Mobiliteit')
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
        ->assertSee('bike@kidicalmass.be');
});
