<?php

return [
    // Partner showcase (PAT-5, home + About narrative pages)
    'strip' => [
        'label' => 'Partenaires et sponsors',
        'see_all' => 'Voir tous les partenaires',
    ],
    'showcase' => [
        'label' => 'Nos partenaires & sponsors',
    ],

    // Funder acknowledgment (footer, site-wide)
    'funder_credit' => 'Avec le soutien de',

    // About/Partners page
    'page' => [
        'heading' => 'Partenaires & sponsors',
        'also_supported_by' => 'Également soutenu par',
        'become_partner' => 'Devenir partenaire ou sponsor ?',
        'hero' => [
            'eyebrow' => 'Partenaires et sponsors',
            'title' => 'Ensemble, nous sommes plus forts pour créer des rues sûres pour les enfants et familles',
        ],
        'allies' => [
            'heading' => 'Nos partenaires et alliés',
            'intro' => 'Ces organisations soutiennent Kidical Mass au niveau national ou régional, par leur financement, leur expertise, leur soutien matériel ou en portant avec nous la volonté de créer des rues plus sûres pour les enfants et les familles.',
            'logo_alt' => 'Les logos des nombreux partenaires et alliés de Kidical Mass, parmi lesquels Brussel Mobiliteit, Pro Velo, Cyclo, GRACQ, Fietsersbond, Bruzz et bien d\'autres',
            'logo_caption' => 'Et bien d\'autres qui contribuent à faire vivre Kidical Mass.',
            'note' => 'Sur le terrain, des partenaires comme Loopz et My Kids Bikes aident également les familles à trouver un vélo adapté.',
        ],
        'why' => [
            'heading' => 'Pourquoi devenir partenaire ou sponsor ?',
            'intro' => 'En soutenant Kidical Mass, vous contribuez concrètement à rendre les villes et les quartiers plus sûrs, plus accueillants et plus adaptés aux enfants.',
            'items' => [
                'Vous contribuez à créer des rues où les enfants et familles peuvent se déplacer à vélo en toute sécurité.',
                'Vous soutenez la participation citoyenne et une mobilité durable, active et inclusive.',
                'Vous bénéficiez d\'une visibilité positive auprès des familles, des habitants et des décideurs locaux.',
            ],
        ],
        'formules' => [
            'heading' => 'Nos formules',
            'intro' => 'Nous proposons des formules simples et accessibles, adaptées aux ASBL, associations et entreprises. Vous choisissez le niveau de soutien et de visibilité qui vous correspond.',
            'vzw' => [
                'title' => 'Pour les ASBL et associations',
                'items' => [
                    ['name' => 'Supporter', 'price' => '100 €/an', 'body' => 'Mention sur nos réseaux sociaux.'],
                    ['name' => 'Partenaire', 'price' => '250 €/an', 'body' => 'Logo sur notre site web + 2 mentions sur nos réseaux sociaux.'],
                    ['name' => 'Partenaire Communauté', 'price' => '500 €/an', 'body' => 'Logo sur notre site web + 4 mentions sur nos réseaux sociaux + logo sur les flyers d\'un événement.'],
                ],
            ],
            'bedrijf' => [
                'title' => 'Pour les entreprises',
                'items' => [
                    ['name' => 'Friend', 'price' => '500 €/an', 'body' => 'Logo sur notre site web + 2 mentions sur nos réseaux sociaux.'],
                    ['name' => 'Sponsor', 'price' => '1 000 €/an', 'body' => 'Logo sur notre site web + 4 mentions sur nos réseaux sociaux + logo sur les flyers de tous nos événements.'],
                    ['name' => 'Partenaire Principal', 'price' => '2 500 €/an', 'body' => 'Logo sur notre site web + 6 mentions sur nos réseaux sociaux + logo sur tous nos flyers et bannières + possibilité d\'avoir un stand ou une présence lors de nos événements, en concertation avec l\'équipe.'],
                ],
            ],
            'vat_note' => 'Tous les montants sont hors TVA.',
            'pdf' => 'Voir toutes les formules et les tarifs (pdf) →',
        ],
        'collab' => [
            'heading' => 'Une collaboration qui a du sens',
            'intro' => 'Kidical Mass est avant tout un mouvement citoyen, pas un espace publicitaire. Nous travaillons avec des partenaires qui partagent nos valeurs : convivialité, sécurité, durabilité et inclusion. Votre soutien peut prendre la forme d\'un financement, de matériel ou d\'un soutien en nature. Il ne peut pas être assorti de conditions qui influencent notre fonctionnement ou notre message.',
            'intro_2' => 'Nous restons indépendants, non commerciaux et libres de refuser ou de mettre fin à toute collaboration qui ne correspondrait plus à nos valeurs.',
            'offer' => [
                'heading' => 'Ce que nous offrons à nos partenaires',
                'lead' => 'Votre soutien permet aussi de vous associer concrètement au mouvement :',
                'items' => [
                    'visibilité sur nos canaux de communication ;',
                    'présence de votre logo sur certains supports et matériels promotionnels ;',
                    'possibilité de participer à nos activités et événements ;',
                    'association à une initiative citoyenne qui agit pour une mobilité plus durable et des villes à hauteur d\'enfant.',
                ],
            ],
            'charter' => 'Lire notre Charte de sponsoring & partenariat (pdf) →',
        ],
        'enquiry' => [
            'heading' => 'Intéressé·e ? Parlons-en.',
            'intro' => 'Dites-nous brièvement qui vous êtes et ce qui vous intéresse. Nous vous contacterons rapidement pour trouver ensemble la formule qui vous correspond. Cela ne vous engage à rien.',
            'fallback_lead' => 'Vous préférez nous contacter directement ?',
            'types' => [
                'asbl' => 'ASBL',
                'association' => 'Association',
                'entreprise' => 'Entreprise',
                'commune' => 'Commune',
                'pouvoir-public' => 'Pouvoir public',
                'autre' => 'Autre',
            ],
            'formules' => [
                'nog-niet-zeker' => 'Pas encore sûr·e',
                'supporter' => 'Supporter (ASBL)',
                'partner' => 'Partenaire (ASBL)',
                'community-partner' => 'Partenaire Communauté (ASBL)',
                'friend' => 'Ami (entreprise)',
                'sponsor' => 'Sponsor (entreprise)',
                'main-partner' => 'Partenaire Principal (entreprise)',
            ],
            'form' => [
                'name' => 'Nom',
                'name_placeholder' => 'Votre nom',
                'email' => 'Adresse mail',
                'email_placeholder' => 'vous@organisation.be',
                'organisation' => 'Organisation',
                'organisation_placeholder' => 'Nom de votre ASBL, entreprise ou association',
                'type' => 'Type d\'organisation',
                'type_placeholder' => 'Choisissez une option',
                'formule' => 'Formule qui vous intéresse',
                'formule_optional' => '(facultatif)',
                'formule_placeholder' => 'Pas encore sûr·e',
                'message' => 'Quelque chose à ajouter ?',
                'message_placeholder' => 'Pourquoi souhaitez-vous collaborer avec nous ? Une question ?',
                'submit' => 'Envoyer ma demande',
                'sending' => 'Envoi en cours…',
                'privacy' => 'Nous utilisons vos données uniquement pour répondre à votre demande. En savoir plus ? Consultez notre déclaration de confidentialité.',
                'success_title' => 'Merci',
                'success_body' => 'Nous avons bien reçu votre demande. Un membre de notre équipe vous contactera prochainement pour trouver ensemble la formule qui vous convient.',
            ],
        ],
    ],
];
