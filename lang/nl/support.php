<?php

// Support / "Steun Kidical Mass" copy — the #1 org goal (recurring support).
// Public verb is "steun", never "lid". See docs/wiki/design/30-skeleton/steun-ons.md.
// People-led rework 2026-06-25: lead with the joy + the cause, then show the
// real work a small team carries (the "load" beat), then make one layered ask.
// No "no paid staff" claim and no headcounts (they change). Support is framed as
// buying back the team's time and as resilience if subsidies stop. Proof numbers
// are sourced from docs/raw/website/* (le-projet, organisation, press); keep them honest.
return [
    // Nav + footer CTA labels
    'nav' => 'Steun ons',
    'cta' => 'Steun Kidical Mass',

    'photos' => [
        'org_1' => 'Drie organisatoren poseren lachend, één met roze hesje en bloemenkrans, tijdens een rit.',
        'org_2' => 'Groepsfoto van de organisatoren in blauwe Kidical Mass-truien met vlag, na een rit.',
    ],

    // Page <title>
    'title' => 'Steun Kidical Mass',

    // Hero — the cause leads; the eyebrow says this is the support page
    'hero' => [
        'eyebrow' => 'Steun ons',
        'title' => 'Samen maken we straten veilig voor kinderen.',
        'cta_note' => 'vanaf €3 per maand',
    ],

    // Mission — why this matters (the driver to give). Shown as an intro-scale
    // lead; no heading above it (the hero already carries the page title).
    'mission' => [
        'body' => 'Een Kidical Mass is een vrolijke en gezinsvriendelijke fietstocht, maar ook een manier om een boodschap te laten horen: kinderen en gezinnen moeten zich in elke buurt veilig met de fiets kunnen verplaatsen. We vragen om afgescheiden fietspaden op belangrijke verkeersassen, fietsenstallingen die aangepast zijn aan gezinnen, veilige schoolomgevingen en zones 30 die ook echt worden gerespecteerd. Jouw steun helpt ons om deze beweging levend te houden, overal in België parades te organiseren en deze eisen bij de gemeenten op tafel te leggen.',
    ],

    // The story — proof + the load in one section. The big movement (stats deck)
    // is carried by a small, stretched team (work chips). The body tells the arc
    // WITHOUT reciting the deck's numbers or the chips' items (no redundancy).
    'story' => [
        'title' => 'Een grote beweging, gedragen door een klein team',
        'body' => 'Wat in 2020 begon met een eerste parade in Brussel, brengt vandaag lokale groepen in heel België samen. Achter al die parades staat een klein team dat hier veel tijd in steekt, vaak naast een andere job.',
        'work' => 'Nieuwe groepen laten groeien, lokale teams begeleiden, parades organiseren, materiaal creëren, financiering zoeken… De beweging is gegroeid en we willen al dit werk nu duurzamer maken. Daar zijn ook middelen voor nodig!',
    ],

    // Proof deck labels. Values are computed live (see App\Support\SupportStats):
    // groups + rides are counted from the database, the participant count is a
    // curated per-year figure (year_stats). Cards with no honest value are hidden.
    'stats' => [
        'groups' => 'lokale groepen in heel België',
        'rides' => 'ritten in :year',
        'participants' => 'kinderen en ouders fietsten mee in :year',
    ],

    // What your support makes possible — a simple green-check checklist (single lines).
    'funds_review' => '',
    'funds' => [
        'title' => 'Wat jouw steun mogelijk maakt',
        'items' => [
            ['title' => 'Meer parades organiseren', 'body' => 'coördinatie, verzekering, praktische kosten en alles wat nodig is om gezinnen samen te laten fietsen.'],
            ['title' => 'De lokale teams uitrusten', 'body' => 'roze hesjes, parademateriaal, signalisatie en alles wat nodig is om de groepen veilig te begeleiden.'],
            ['title' => 'Kidical Mass bekendmaken', 'body' => 'website, sociale media, flyers, affiches en communicatiemateriaal zodat gezinnen weten wanneer en waar ze kunnen aansluiten.'],
            ['title' => 'Vrijwilligers opleiden en begeleiden', 'body' => 'opleidingen rond begeleiding, veiligheid en het uitstippelen van routes, maar ook ondersteuning voor groepen die van start gaan.'],
            ['title' => 'De beweging laten groeien', 'body' => 'nieuwe groepen helpen opstarten en hen de tools geven om hun eigen parades te organiseren.'],
            ['title' => 'Kidical Mass het hele jaar door laten leven', 'body' => 'nationale coördinatie, ontmoetingen tussen groepen, partnerschappen en acties om onze boodschap ook buiten de parades uit te dragen.'],
            ['title' => 'Het team dat de beweging draaiende houdt eerlijk vergoeden', 'body' => 'want een groeiende beweging heeft tijd en mensen nodig om haar te laten leven.'],
        ],
    ],

    // The ask — now the page's single, closing CTA (full-bleed yellow band).
    // Carries the €3 framing, the t-shirt token, and the non-negotiable
    // "meefietsen blijft gratis" reassurance. The white ask card was removed
    // (it duplicated this band); its disclaimer folded in here.
    'ask' => [
        'title' => 'Steun vanaf €3 per maand',
        'body' => 'Met een regelmatige bijdrage help je ons een beweging op te bouwen die minder afhankelijk is van subsidies, de Kidical Mass-beweging overal in België verder te laten groeien en onze boodschap kracht bij te zetten. Als dank voor je steun ontvang je een superleuk t-shirt van biologisch katoen in de kleuren van Kidical Mass, om met trots te dragen. De parades blijven uiteraard altijd gratis.',
        'cta' => 'Steun maandelijks',
        'button' => 'Steun Kidical Mass',
        'note' => 'Je wordt doorgestuurd naar Growfunding. De betalingen worden rechtstreeks via hun platform verwerkt.',
    ],

    // Contextual callouts (one component, two variants)
    'callout' => [
        'home' => [
            'title' => 'Kidical Mass blijft gratis. Dankzij mensen zoals jij.',
            'body' => 'Steun vanaf €3 per maand en help veilige straten voor kinderen waarmaken, in elke buurt. Meefietsen blijft gratis.',
        ],
        'event' => [
            'title' => 'Fijn meegereden? Steun de volgende rit.',
            'body' => 'Met jouw steun, vanaf €3 per maand, houden we de beweging draaiende en blijven we fietsparades organiseren voor veiligere straten voor kinderen en gezinnen, in elke buurt.',
        ],
    ],
    // One-off donation — a French-only, client-decision placeholder. Kept empty
    // here (the block self-hides) so the gate is translation-driven, not locale-coded.
    'donation' => [
        'heading' => '',
        'body' => '',
        'iban' => '',
    ],
];
