<?php

// Over ons — copy for the About section (P-14 → P-20). Stats labels here feed
// App\Support\AboutStats; the values are live counts + the curated Jaarcijfers
// row. Page copy groups (mission/vision/organisation/press) follow the
// support.php precedent: words live here, structure lives in the Blade views.
return [
    'stats' => [
        'groups' => 'lokale groepen in heel België',
        'rides' => 'fietsparades sinds 2020',
        'volunteers' => 'actieve vrijwilligers',
        'participants' => 'deelnemers in :year',
    ],

    // Wat we doen (P-15). Structure: story column (intro + welkom + quote +
    // the three axes as subtitled body text) with the stat deck beside it,
    // then a chained closing CTA.
    'mission' => [
        'title' => 'Veilige straten, voor elk kind',
        'intro_1' => 'Kidical Mass Belgium is een nationaal netwerk van lokale groepen die feestelijke, veilige en kindvriendelijke fietsparades organiseren in heel België. We begonnen in 2020 in Brussel en groeien nog elk jaar, in Brussel, Wallonië en Vlaanderen.',
        'intro_2' => 'Elke fietsparade heeft muziek onderweg. We rijden op het tempo van het jongste kind, op zorgvuldig gekozen routes, begeleid door getrainde vrijwilligers in opvallende roze hesjes. Kidical Mass is een manier om samen je buurt te ontdekken, nieuwe mensen te leren kennen en zelfvertrouwen op de fiets te winnen. Voor de kinderen, en vaak ook voor de ouders.',
        'welcome' => [
            'title' => 'Iedereen is welkom',
            'body' => 'Je hoeft geen ervaren fietser te zijn. Nog nooit in het verkeer gefietst? Dat geeft niets: voor veel ouders is een rit de eerste keer op de baan, en onze begeleiders zorgen dat niemand er alleen voor staat. Je hoeft geen fiets te hebben en je hoeft niet uit de buurt te komen. Kidical Mass is gemaakt om de volledige diversiteit van elke gemeente te weerspiegelen, en om elke drempel weg te nemen die een gezin kan tegenhouden.',
            'link' => 'Geen fiets of nog nooit meegereden? Voor het eerst mee →',
        ],
        'quote' => [
            'text' => 'Wat hij zo leuk vindt aan fietsen, denk ik, is die vrijheid om buiten te zijn, lucht te hebben, er alleen op uit te trekken. Hij wil altijd ver gaan, iets nieuws ontdekken.',
            'attribution' => 'Julienne, mama van twee kinderen (2 en 5 jaar)',
        ],
        'axes' => [
            'title' => 'Drie dingen die we doen',
            'item_1' => [
                'title' => 'We helpen nieuwe groepen starten',
                'body' => 'Elke Kidical Mass begint met een handvol mensen die iets beters willen voor hun buurt. Wij begeleiden hen van de eerste vergadering tot de eerste rit.',
            ],
            'item_2' => [
                'title' => 'We ondersteunen wie al rijdt',
                'body' => 'Lokale groepen krijgen vorming, materiaal en nationale zichtbaarheid, zodat zij zich kunnen richten op wat telt: mensen samenbrengen.',
            ],
            'item_3' => [
                'title' => 'We pleiten voor kindvriendelijke straten',
                'body' => 'Vrolijke parades zijn een begin, geen eindpunt. Samen met steden en regio\'s werken we aan veiligere infrastructuur, trager verkeer en straten die kinderen en gezinnen echt verwelkomen.',
            ],
        ],
        'closing' => [
            'heading' => 'Wat willen we veranderen?',
            'label' => 'Lees wat we vragen',
        ],
    ],

    // Wat we vragen (P-16). Structure: tightened statement, 4 demands with
    // parent voices nested under the demand they speak to, manifest info-card,
    // closing chained to Hoe we werken. Register: committed, not preachy (ToV).
    'vision' => [
        'title' => 'Een stad op kindermaat',
        'statement_1' => 'We geloven dat elk kind in België zich veilig en met vertrouwen door zijn stad moet kunnen bewegen. Dat straten ontworpen horen te zijn voor de mensen die er wonen, niet alleen voor de auto\'s die er passeren. En dat kinderen mee mogen beslissen over hoe hun buurt eruitziet.',
        'statement_2' => 'Dat is niet radicaal. Het is wat de meeste ouders willen, het is wat onderzoek bevestigt, en het is waar we naartoe werken: één rit, één gemeenteraad, één beleidsgesprek tegelijk.',
        'demands' => [
            'title' => 'Wat we vragen',
            'item_1' => [
                'title' => 'Veilige fietsinfrastructuur voor kinderen en gezinnen',
                'body' => 'Aparte fietspaden die kinderen echt kunnen gebruiken: gescheiden van het verkeer, goed onderhouden en aaneengesloten. Gebouwd voor de kleinste fietsers, niet alleen voor de snelste.',
            ],
            'item_2' => [
                'title' => 'Tragere, rustigere woonstraten',
                'body' => 'Minder snel en minder druk verkeer in de straten waar kinderen wonen en spelen. Twintig is genoeg, en handhaving telt evenveel als borden.',
            ],
            'item_3' => [
                'title' => 'Openbare ruimte die kinderen en gezinnen echt verwelkomt',
                'body' => 'Parken, pleinen en straten waar kinderen kind kunnen zijn: luidruchtig, nieuwsgierig, in beweging. Ruimte die werkt voor kinderwagens en bakfietsen, niet alleen voor auto\'s en gehaaste volwassenen.',
            ],
            'item_4' => [
                'title' => 'De stem van kinderen in beslissingen over hun omgeving',
                'body' => 'Kinderen zijn experts van hun eigen buurt. Ze verdienen echte inspraak, geen symbolisch gebaar, wanneer steden straten, parken en openbare ruimte plannen.',
            ],
            'closing' => 'Vier vragen, één rode draad: kinderen moeten zelf op pad kunnen. Zolang dat niet vanzelfsprekend is, komen we ermee de straat op: fietsend, zingend, met honderden tegelijk.',
        ],
        'quotes' => [
            'fatima' => [
                'text' => 'Ik ben constant bang voor de auto\'s, de trams. Tegen dat we thuis zijn van school, ben ik uitgeput.',
                'attribution' => 'Fatima, mama van drie kinderen, Jette',
            ],
            'camille' => [
                'text' => 'Ik heb het gevoel dat ik de hele tijd de levenslust van mijn kinderen afrem.',
                'attribution' => 'Camille, mama van twee kinderen, Sint-Gillis',
            ],
        ],
        'manifest' => [
            'label' => 'Het manifest',
            'body' => 'Onze volledige visie op papier, mee ondertekend door een coalitie van Belgische verenigingen. Lees het en deel het.',
            'link' => 'Download het manifest',
        ],
        'closing' => [
            'heading' => 'Wie maakt dit waar?',
            'label' => 'Ontdek hoe we werken',
        ],
    ],

    // Hoe we werken (P-17). Structure: two-paragraph intro (lokaal-eerst +
    // no-HQ; the national/local detail lives in the lists, distill 2026-07-04),
    // two titled lists (nationaal | lokaal), the duo (safety folded into their
    // text), closing to Meehelpen. No bespoke components (variant A).
    'organisation' => [
        'title' => 'Buren die de straat op trekken',
        'intro_1' => 'Kidical Mass Belgium is zo opgebouwd dat het overal echt lokaal blijft. Geen nationale campagne met lokale filialen, maar een netwerk van groepen die elk hun eigen buurt kennen.',
        'intro_2' => 'Er is geen hoofdkantoor: Kidical Mass draait op vrijwilligers, gedragen door mensen zoals jij.',
        'who' => [
            'title' => 'Wie wat doet',
        ],
        'national' => [
            'title' => 'Nationale coördinatie',
            'items' => [
                'Bewaakt het merk en de identiteit van Kidical Mass Belgium',
                'Ontwikkelt vorming en onboarding voor nieuwe trekkers',
                'Coördineert nationale communicatie, website en pers',
                'Beheert partnerschappen en dient subsidieaanvragen in voor het hele netwerk',
            ],
        ],
        'local' => [
            'title' => 'Lokale afdelingen',
            'items' => [
                'Organiseren hun eigen fietsparades, met eigen routes en verzamelpunten',
                'Werven en begeleiden lokale vrijwilligers',
                'Bouwen banden met lokale partners en de gemeente',
                'Zíjn de beweging. De coördinatie bestaat om hen te steunen, niet andersom.',
            ],
        ],
        'duo' => [
            'title' => 'Het coördinatieduo',
            'body_1' => 'Leticia en Cecilia vormen samen het coördinatieduo. Zij zijn het centrale aanspreekpunt voor lokale groepen en vrijwilligers: ze organiseren vorming voor veilige begeleiding, lossen dagelijkse vragen op en bewaken de basiskwaliteit en veiligheid van elke rit.',
            'body_2' => 'Alle afdelingen werken daarvoor met dezelfde veiligheidsafspraken en routerichtlijnen: elke route loopt langs parken, speelpleinen en veilige infrastructuur, en waar nodig stemmen organisatoren de route vooraf af met de lokale politie.',
            'link' => 'Hoe een rit praktisch verloopt: Voor het eerst mee →',
        ],
        'closing' => [
            'heading' => 'Een afdeling starten of vervoegen?',
            'label' => 'Zo doe je mee',
        ],
    ],

    // Nieuws (P-18). The feed itself is admin content (Article); only the
    // page chrome lives here. The empty body carries :instagram/:facebook
    // placeholders that the view fills with links (URLs: config/kidicalmass.php).
    'news' => [
        'title' => 'Nieuws uit de beweging',
        'lead' => 'Updates van de beweging: nieuwe afdelingen, mijlpalen en verhalen van onderweg.',
        'empty' => [
            'title' => 'De eerste verhalen komen eraan',
            'body' => 'We zijn nog maar net begonnen met schrijven. Kom binnenkort eens terug, of volg ons op :instagram en :facebook voor nieuws zodra het er is.',
        ],
        'national' => 'Heel België',
        'gallery' => 'Foto\'s',
        'more' => [
            'title' => 'Meer nieuws',
        ],
    ],

    // Pers (P-19). Structure: the year-grouped archive left, a sticky
    // perscontact card right (its label replaces a contact heading). No outlet
    // strip (the archive shows the outlets), no closing CTA (the page IS the
    // contact).
    'press' => [
        'title' => 'Het verhaal van de beweging',
        'overview' => [
            'title' => 'In de pers',
        ],
        'empty' => [
            'title' => 'We bouwen aan een persoverzicht',
            'body' => 'Kidical Mass kwam de afgelopen jaren in heel wat kranten, radio en tv. We brengen die berichtgeving binnenkort samen op één plek. Schreef je over Kidical Mass en wil je dat je artikel hier verschijnt? Laat het ons weten via :email.',
        ],
        'contact' => [
            'label' => 'Perscontact',
            'body' => 'We brengen je in contact met lokale trekkers en gezinnen, delen cijfers en achtergrond bij de beweging, en regelen een fotomoment bij een volgende fietsparade.',
            'note' => 'Je hoort snel van ons.',
        ],
        'background' => [
            'link' => 'Achtergrond en cijfers: lees wat we doen →',
        ],
        'document' => [
            'label' => 'Artikel',
        ],
    ],
];
