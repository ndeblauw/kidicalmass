<?php

// Support / "Soutenir Kidical Mass" copy — French, from the copywriting doc
// (section "Steun ons"). The funds list carries 7 titled items (NL keeps its 4
// short lines); the one-off donation is a client decision, shown as a marked
// placeholder.

return [
    // Nav + footer CTA labels
    'nav' => 'Nous soutenir',
    'cta' => 'Soutenir Kidical Mass',

    // Page <title>
    'title' => 'Soutenir Kidical Mass',

    // Hero — the cause leads; the eyebrow says this is the support page
    'hero' => [
        'eyebrow' => 'Nous soutenir',
        'title' => 'Ensemble, rendons les rues plus sûres pour les enfants et les familles.',
        'cta_note' => 'À partir de 3 € par mois',
    ],

    // Mission — why this matters (the driver to give)
    'mission' => [
        'body' => 'Une Kidical Mass est une parade à vélo joyeuse et familiale, mais aussi une manière de faire entendre un message : les enfants et familles doivent pouvoir se déplacer à vélo en toute sécurité, dans chaque quartier. Nous demandons des pistes cyclables séparées sur les grands axes, des parkings vélo adaptés aux familles, des abords d\'écoles sécurisés et des zones 30 réellement respectées. Votre soutien nous aide à faire vivre ce mouvement, à organiser des parades partout en Belgique et à porter ces revendications auprès des communes.',
    ],

    // The story — proof + the load in one section
    'story' => [
        'title' => 'Un grand mouvement porté par une petite équipe',
        'body' => 'Ce qui a commencé en 2020 avec une première parade à Bruxelles rassemble aujourd\'hui des groupes locaux partout en Belgique. Derrière toutes ces parades, il y a une petite équipe qui y consacre beaucoup de temps, souvent en plus d\'un autre job.',
        'work' => 'Faire grandir de nouveaux groupes, accompagner les équipes locales, organiser les parades, créer du matériel, chercher des financements… Le mouvement a grandi, et nous voulons maintenant rendre tout ce travail plus durable, ce qui demande aussi des moyens !',
    ],

    // Proof deck labels (values are computed live)
    'stats' => [
        'groups' => 'groupes locaux en Belgique',
        'rides' => 'parades en :year',
        'participants' => 'enfants et parents ont pédalé ensemble lors des Kidical Mass en :year',
    ],

    // What your support makes possible — 7 titled items (vs NL's 4 short lines).
    // The French item count leads; every item carries "To be confirmed".
    'funds' => [
        'title' => 'Ce que votre soutien rend possible',
        'items' => [
            ['title' => 'Organiser davantage de parades', 'body' => 'coordination, assurance, frais pratiques et tout ce qu\'il faut pour que les familles puissent rouler ensemble.'],
            ['title' => 'Équiper les équipes locales', 'body' => 'gilets roses, matériel de parade, signalétique et tout le nécessaire pour accompagner les cortèges en toute sécurité.'],
            ['title' => 'Faire connaître les Kidical Mass', 'body' => 'site web, réseaux sociaux, flyers, affiches et matériel de communication pour que les familles sachent quand et où nous rejoindre.'],
            ['title' => 'Former et accompagner les bénévoles', 'body' => 'formations à l\'encadrement, à la sécurité et à la création de parcours, mais aussi soutien aux groupes qui se lancent.'],
            ['title' => 'Faire grandir le mouvement', 'body' => 'aider de nouveaux groupes à voir le jour et leur donner les outils pour organiser leurs propres parades.'],
            ['title' => 'Faire vivre Kidical Mass toute l\'année', 'body' => 'coordination nationale, rencontres entre groupes, partenariats et actions pour porter notre message au-delà des parades.'],
            ['title' => 'Rémunérer justement le travail de l\'équipe', 'body' => 'parce qu\'un mouvement qui grandit a besoin de temps et de personnes pour le faire vivre.'],
        ],
    ],

    // The ask — the page's closing CTA (full-bleed yellow band)
    'ask' => [
        'title' => 'Soutenez le mouvement dès 3 € par mois',
        'body' => 'Avec un soutien régulier, vous nous aidez à construire un mouvement moins dépendant des subsides, à continuer à faire grandir les Kidical Mass partout en Belgique et à porter nos revendications. Et pour vous remercier de votre soutien, vous recevez un super t-shirt en coton bio aux couleurs de Kidical Mass, à porter fièrement. Les parades, elles, restent toujours gratuites.',
        'cta' => 'Soutenir mensuellement',
        'button' => 'Soutenir Kidical Mass',
        'note' => 'Vous serez redirigé·e vers Growfunding. Les paiements sont gérés directement par leur plateforme.',
    ],

    // One-off donation — the client's call (open decision); the bank account
    // is a placeholder until they confirm it.
    'donation' => [
        'heading' => 'Faire un don unique',
        'body' => 'Vous préférez soutenir le mouvement en un seul geste ? Un don unique est également le bienvenu.',
        'iban' => 'Numéro de compte (IBAN) : à confirmer',
    ],
];
