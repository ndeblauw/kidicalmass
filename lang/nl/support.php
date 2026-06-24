<?php

// Support / "Steun Kidical Mass" copy — the #1 org goal (recurring support).
// Public verb is "steun", never "lid". See docs/wiki/design/30-skeleton/steun-ons.md.
// Mission-led rebuild 2026-06-05: lead with the why (safe streets for kids),
// back it with real proof of impact, then make the ask. Numbers are sourced
// from docs/raw/website/* (le-projet, organisation, press) — keep them honest.
return [
    // Nav + footer CTA labels
    'nav' => 'Steun ons',
    'cta' => 'Steun Kidical Mass',

    // Page <title>
    'title' => 'Steun Kidical Mass',

    // Hero — the cause leads; the eyebrow says this is the support page
    'hero_eyebrow' => 'Steun ons',
    'hero_title' => 'Samen maken we straten veilig voor kinderen.',

    // Mission — why this matters (the driver to give)
    'mission_title' => 'Meer dan een fietstocht',
    'mission_body' => 'Een Kidical Mass is een feest op wielen: muziek, roze hesjes, straten vol kinderen op de fiets. En tegelijk is het een duidelijke vraag aan onze steden: geef kinderen straten waar ze veilig kunnen fietsen, in elke buurt. Elke rit laat zien hoe zo’n stad eruitziet. We doen het met vrijwilligers, zonder betaalde staf. Jouw steun houdt de beweging onafhankelijk en laat ze groeien naar nieuwe buurten.',

    // Proof of impact — concrete, sourced, safe to publish
    'proof_title' => 'Van één rit naar heel België',
    'proof_body' => 'Wat in 2020 begon met één fietstocht in Brussel, groeide uit tot een beweging in heel het land. Vandaag rijden we met zo’n 60 ritten per jaar, gedragen door ruim honderd vrijwilligers in 16 van de 19 Brusselse gemeenten en in steden in Wallonië en Vlaanderen. In 2024 fietsten meer dan 5.500 kinderen en ouders mee.',
    'proof_stats' => [
        ['value' => '5.500', 'label' => 'kinderen en ouders fietsten mee in 2024'],
        ['value' => '60+', 'label' => 'ritten per jaar'],
        ['value' => '16/19', 'label' => 'Brusselse gemeenten, plus Wallonië en Vlaanderen'],
    ],
    'proof_press' => 'Te zien bij RTBF, Bruzz, Het Laatste Nieuws en BX1.',
    'proof_backers' => 'Met steun van Brussel Mobiliteit, Stad Brussel, gemeente Schaarbeek, Cera en Clean Cities.',

    // What your support makes possible (promises-band cards: title + body)
    'funds_title' => 'Wat jouw steun mogelijk maakt',
    'funds' => [
        'streets' => [
            'icon' => 'map',
            'title' => 'Veilige straten, buurt per buurt',
            'body' => 'Elke nieuwe rit zet kindvriendelijke straten op de kaart, in steeds meer steden.',
        ],
        'safety' => [
            'icon' => 'shield-check',
            'title' => 'Veilig op weg',
            'body' => 'Materiaal en opleiding voor de roze hesjes die elke rit veilig begeleiden.',
        ],
        'people' => [
            'icon' => 'users',
            'title' => 'Gedragen door mensen, niet door subsidies',
            'body' => 'Met jouw steun blijft de beweging onafhankelijk en in handen van bewoners zoals jij.',
        ],
    ],

    // The ask (primary) — meaning leads, the t-shirt is the token beneath
    'ask_title' => 'Steun vanaf €3 per maand',
    'ask_body' => 'Vanaf €3 per maand help je veilige straten voor kinderen waarmaken, in elke buurt. Je krijgt een t-shirt om je steun te dragen.',
    'ask_cta' => 'Steun maandelijks',
    'ask_where' => 'We werken met vrijwilligers, zonder betaalde staf. Je steun gaat naar hesjes, opleiding en nieuwe ritten in nieuwe buurten.',
    'ask_note' => 'Je gaat naar Growfunding. Wij verwerken zelf geen betalingen.',

    // Riding stays free (the non-negotiable reassurance)
    'free_title' => 'Meefietsen blijft altijd gratis',
    'free_body' => 'Je steunt zodat het gratis kan blijven, voor iedereen.',

    // All tiers
    'tiers' => 'Meer geven? Bekijk alle tiers op Growfunding',

    // Movement scale (no backer count) — used as the closing CTA band
    'scale' => 'Elke maand rijden honderden gezinnen mee.',
    'scale_sub' => 'Doe mee en help ons veilige straten waarmaken, in heel België.',

    // Contextual callouts (one component, two variants)
    'home_title' => 'Kidical Mass blijft gratis. Dankzij mensen zoals jij.',
    'home_body' => 'Steun vanaf €3 per maand en help veilige straten voor kinderen waarmaken, in elke buurt. Meefietsen blijft gratis.',
    'event_title' => 'Fijn meegereden? Steun de volgende rit.',
    'event_upcoming_title' => 'Zin om mee te rijden? Steun de volgende rit.',
    'event_body' => 'Met €3 per maand zorg je dat er volgende maand weer een rit is. Meefietsen blijft altijd gratis.',
];
