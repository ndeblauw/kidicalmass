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

    // Page <title>
    'title' => 'Steun Kidical Mass',

    // Hero — the cause leads; the eyebrow says this is the support page
    'hero_eyebrow' => 'Steun ons',
    'hero_title' => 'Samen maken we straten veilig voor kinderen.',
    'hero_cta_note' => 'vanaf €3 per maand',

    // Mission — why this matters (the driver to give). Shown as an intro-scale
    // lead; no heading above it (the hero already carries the page title).
    'mission_body' => 'Een Kidical Mass is een feest op wielen: muziek, roze hesjes, straten vol kinderen op de fiets. En tegelijk is het een duidelijke vraag aan onze steden: geef kinderen straten waar ze veilig kunnen fietsen, in elke buurt. Jouw steun houdt die beweging draaiende en laat ze groeien, buurt na buurt.',

    // The story — proof + the load in one section. The big movement (stats deck)
    // is carried by a small, stretched team (work chips). The body tells the arc
    // WITHOUT reciting the deck's numbers or the chips' items (no redundancy).
    'story_title' => 'Een grote beweging op de schouders van een klein team',
    'story_body' => 'Wat in 2020 begon met één fietstocht in Brussel, rijdt vandaag door heel het land. Achter al die ritten zit een klein team dat er veel tijd in steekt, vaak naast een gewone job.',
    // The team's ongoing work, as a flowing second paragraph (was a row of chips).
    'story_work' => 'Dat betekent nieuwe groepen op weg helpen, ritten organiseren, teams begeleiden, materiaal maken en subsidies zoeken. De beweging groeide prachtig, en nu willen we het werk erachter houdbaar maken.',
    // Proof deck labels. Values are computed live (see App\Support\SupportStats):
    // groups + rides are counted from the database, the participant count is a
    // curated per-year figure (year_stats). Cards with no honest value are hidden.
    'stat_groups' => 'lokale groepen in heel België',
    'stat_rides' => 'ritten in :year',
    'stat_participants' => 'kinderen en ouders fietsten mee in :year',

    // What your support makes possible — a simple green-check checklist (single lines).
    'funds_title' => 'Wat jouw steun mogelijk maakt',
    'funds' => [
        'Meer tijd voor ritten en nieuwe groepen, minder voor het najagen van subsidies',
        'Materiaal en opleiding voor de roze hesjes die elke rit begeleiden',
        'Een beweging die blijft draaien, ook als subsidies wegvallen',
        'Een team dat eerlijk vergoed wordt voor het werk dat het nu al doet',
    ],

    // The ask — now the page's single, closing CTA (full-bleed yellow band).
    // Carries the €3 framing, the t-shirt token, and the non-negotiable
    // "meefietsen blijft gratis" reassurance. The white ask card was removed
    // (it duplicated this band); its disclaimer folded in here.
    'ask_title' => 'Steun vanaf €3 per maand',
    'ask_body' => 'Je krijgt een t-shirt om je steun te dragen. Hoe meer vaste steun, hoe minder we moeten leunen op subsidies. En meefietsen blijft altijd gratis.',
    'ask_cta' => 'Steun maandelijks',
    'ask_note' => 'Je gaat naar Growfunding. Wij verwerken zelf geen betalingen.',

    // Contextual callouts (one component, two variants)
    'home_title' => 'Kidical Mass blijft gratis. Dankzij mensen zoals jij.',
    'home_body' => 'Steun vanaf €3 per maand en help veilige straten voor kinderen waarmaken, in elke buurt. Meefietsen blijft gratis.',
    'event_title' => 'Fijn meegereden? Steun de volgende rit.',
    'event_body' => 'Met €3 per maand zorg je dat er volgende maand weer een rit is. Meefietsen blijft altijd gratis.',
];
