<?php

return [
    'types' => [
        'ride' => 'Parade à vélo',
        'meeting' => 'Réunion',
        'workshop' => 'Atelier',
        'other' => 'Activité',
    ],
    'past_badge' => 'Passées',
    'facts' => [
        'when' => 'Quand',
        'departure' => 'Point de départ',
        'distance' => 'Distance',
        'duration' => 'Durée',
        'participation' => 'Participation',
        'free' => 'Gratuit · Pas d\'inscription nécessaire',
        'route' => 'Itinéraire',
        'komoot' => 'Voir sur Komoot',
        'komoot_button' => 'Voir sur Komoot',
    ],
    'gallery' => [
        'title' => 'En images',
    ],
    'share' => [
        'past_heading' => 'Partagez les bons souvenirs',
        'past_body' => 'Faites découvrir à votre entourage comme c\'était chouette !',
        'upcoming_heading' => 'Des amis pour la prochaine ?',
        'upcoming_body' => 'Invitez vos proches à vous rejoindre. Parce qu\'à plusieurs, c\'est encore plus chouette !',
        'upcoming_body_basic' => 'Invitez vos proches à vous rejoindre. Parce qu\'à plusieurs, c\'est encore plus chouette !',
    ],
    'basic' => [
        'facts' => [
            'location' => 'Lieu',
        ],
        'more_info' => 'Plus d\'infos',
        'more_info_volunteers' => 'Plus d\'infos pour les bénévoles',
        'organizer' => 'Organisé par les bénévoles de',
        'closing_heading' => 'Plus d\'activités de Kidical Mass :name ?',
        'closing_label' => 'Voir la page du groupe',
    ],
    'expect' => [
        'heading' => 'À quoi s\'attendre ?',
        'body' => 'C\'est votre première Kidical Mass ? Aucun souci ! Une Kidical Mass est une parade à vélo joyeuse et familiale dans votre quartier, à un rythme adapté aux enfants. Des bénévoles sécurisent les carrefours pour que tout le monde puisse rouler sereinement. Pas besoin de s\'inscrire ni de savoir faire quoi que ce soit de particulier : venez simplement et pédalez avec nous.',
        'cta_getting_started' => 'Comment ça marche ?',
        'cta_group' => 'Découvrez le groupe de :name',
        'photos' => [
            'expect_1' => 'Des enfants roulent ensemble à travers un carrefour pendant une parade.',
            'expect_2' => 'Un vélo cargo portant le logo Kidical Mass avec des enfants qui saluent en chemin.',
            'team_1' => 'Trois bénévoles en gilet rose, prêts à encadrer une parade.',
            'team_2' => 'L\'équipe organisatrice célèbre ensemble la fin d\'une parade réussie.',
        ],
    ],
    'team' => [
        'thanks_prefix' => 'Grâce à des voisins comme vous.',
        'heading' => 'Derrière cette parade, des bénévoles font vivre le mouvement.',
        'body' => 'Ce sont eux qui imaginent les parcours, organisent les sorties et sécurisent les carrefours pour que tout le monde puisse pédaler en toute sécurité.',
        'others' => 'et :count autres',
        'and' => 'et',
        'credit' => [
            'singular' => 'a contribué à rendre cette parade possible.',
            'plural' => 'ont contribué à rendre cette parade possible.',
        ],
    ],
    /*
     * DRAFT, awaiting the client's own bullets (NL + FR) after the 22/9 meeting.
     * Nico will move this to an admin-editable field. Format: one bullet per
     * line, optional "Label: text" (parsed by App\Support\RideText::goodToKnowItems).
     */
    'good_to_know' => [
        'heading' => 'Bon à savoir',
        'label_suffix' => "\u{00A0}:",
        'items' => <<<'TEXT'
            Parents : vous restez responsables de vos enfants pendant toute la parade.
            Casque : conseillé, mais pas obligatoire.
            Les plus petits : ils roulent avec un adulte, en vélo cargo, sur un siège enfant ou sur leur propre vélo juste à côté.
            Photos : nous prenons des photos en route. En participant, vous acceptez qu'on les partage sur nos canaux.
            TEXT,
    ],
    'extra_info' => [
        'from_group' => 'De la part de Kidical Mass :name',
        'from_organisers' => 'De la part des organisateurs',
    ],
    'closing' => [
        'past_heading' => 'Plus de parades Kidical Mass de :name ?',
        'past_label' => 'Découvrez le groupe',
        'upcoming_heading' => 'Envie de participer à la prochaine sortie ?',
        'upcoming_lead' => 'Chaque mois, toutes les sorties des prochaines semaines dans votre boîte mail.',
    ],
];
