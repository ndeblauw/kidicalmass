<?php

// Over ons — copy for the About section (P-14 → P-20). Stats labels here feed
// App\Support\AboutStats; the values are live counts + the curated Jaarcijfers
// row. Page copy groups (mission/vision/organisation/press) follow the
// support.php precedent: words live here, structure lives in the Blade views.
return [
    'hub' => [
        'title' => 'Over ons',
        'hero' => [
            'eyebrow' => 'Over ons',
            'title' => 'Samen maken we van onze straten een plek waar kinderen en gezinnen hun plaats hebben.',
        ],
        'intro' => 'Kidical Mass Belgium is een nationaal netwerk van lokale groepen die overal in België feestelijke, veilige fietstochten organiseren op het tempo van kinderen. De beweging ontstond in 2020 in Brussel en groeit elk jaar verder, in Brussel, Wallonië en Vlaanderen. Vandaag brengt ze gezinnen, vrijwilligers en partners samen die de Kidical Mass-parades in hun buurt mee tot leven brengen. Ons doel: meer kinderen en ouders zin geven om zich elke dag én voor het plezier met de fiets te verplaatsen.',
        'exits' => [
            'aria' => 'Meteen iets regelen',
            'lead' => 'Meteen iets regelen?',
            'items' => [
                'Een groep starten of meehelpen',
                'Ik ben pers',
                'Partner of sponsor worden',
                'De beweging steunen',
            ],
        ],
        'read' => [
            'title' => 'Of lees meer over de beweging',
            'descs' => [
                'Fietsparades, lokale groepen en de weg naar veilige straten.',
                'Vier concrete eisen voor steden en gemeenten. Afgescheiden fietspaden op belangrijke verkeersassen, fietsenstallingen die aangepast zijn aan gezinnen, veilige schoolomgevingen en zones 30 die ook echt worden gerespecteerd.',
                'Lokaal geworteld, licht gecoördineerd, gedragen door vrijwilligers.',
                'Nieuwe afdelingen, mijlpalen en verhalen van onderweg.',
            ],
        ],
        'closing' => [
            'heading' => 'Rij mee met de buurt',
            'label' => 'Vind een rit',
        ],
    ],
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
        'title' => 'Samen maken we van onze straten een plek waar kinderen en gezinnen hun plaats hebben.',
        'intro_1' => 'Kidical Mass Belgium is een nationaal netwerk van lokale groepen die overal in België feestelijke, veilige fietstochten organiseren op het tempo van kinderen. De beweging ontstond in 2020 in Brussel en groeit elk jaar verder, in Brussel, Wallonië en Vlaanderen. Vandaag brengt ze gezinnen, vrijwilligers en partners samen die de Kidical Mass-parades in hun buurt mee tot leven brengen. Ons doel: meer kinderen en ouders zin geven om zich elke dag én voor het plezier met de fiets te verplaatsen.',
        'intro_2' => 'Maar onze parades brengen ook een boodschap: we vragen steden en gemeenten om straten waar kinderen zich écht veilig met de fiets kunnen verplaatsen. Dat betekent vier prioriteiten: afgescheiden fietspaden op belangrijke verkeersassen, fietsenstallingen die aangepast zijn aan gezinnen, veilige schoolomgevingen en zones 30 die ook echt worden gerespecteerd.',
        'welcome' => [
            'title' => 'Iedereen is welkom',
            'body' => 'Je hoeft geen ervaren fietser te zijn, geen perfecte fiets te hebben en zelfs niet in de buurt te wonen. We willen dat elke Kidical Mass de diversiteit van haar gemeente weerspiegelt en toegankelijk is voor zoveel mogelijk mensen. We zoeken oplossingen om drempels weg te nemen die gezinnen ervan kunnen weerhouden om mee te doen, in het bijzonder voor kinderen die in een kwetsbare situatie leven of een handicap hebben. Heb je geen fiets? Dan helpen we je een oplossing te vinden, voor een klein bedrag of zelfs helemaal gratis.',
            'link' => 'Geen fiets of nog nooit meegereden? Voor het eerst mee →',
        ],
        'quote' => [
            'text' => 'Wat hij zo leuk vindt aan fietsen, denk ik, is die vrijheid om buiten te zijn, lucht te hebben, er alleen op uit te trekken. Hij wil altijd ver gaan, iets nieuws ontdekken.',
            'attribution' => 'Julienne, mama van twee kinderen (2 en 5 jaar)',
        ],
        'axes' => [
            'title' => 'Onze activiteiten rond 3 belangrijke pijlers',
            'item_1' => [
                'title' => 'We zetten gezinnen op de fiets',
                'body' => 'We organiseren regelmatig parades en activiteiten om kinderen van 3 tot 12 jaar – en hun ouders – zin te geven om de fiets te nemen. Voor beginners én jonge fietsers creëren we een geruststellende omgeving die drempels rond verkeer, gebrek aan ervaring of gewoon de praktische organisatie helpt wegnemen.',
            ],
            'item_2' => [
                'title' => 'We helpen gezinnen om elke dag te fietsen',
                'body' => 'Onze begeleide fietstochten helpen kinderen en ouders om meer vertrouwen te krijgen in het verkeer en hun fiets beter te leren beheersen. We delen ook tips om een fiets te kiezen, te gebruiken en te onderhouden, en helpen gezinnen die geen fiets hebben om betaalbare oplossingen te vinden. Het doel: ervoor zorgen dat de fiets een regelmatig vervoermiddel kan worden, en niet alleen een activiteit op zondag.',
            ],
            'item_3' => [
                'title' => 'We bouwen aan een fietscultuur',
                'body' => 'Elke Kidical Mass brengt gezinnen, buurtbewoners, verenigingen, gemeenten en lokale organisaties met elkaar in contact. Door hun buurt met de fiets te ontdekken en hun plek in de openbare ruimte in te nemen, laten kinderen zien dat een stad ook voor hen kan worden ingericht. Met onze acties en eisen laten we hun stem horen voor veiligere en aangenamere straten.',
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
        'title' => 'Straten op kindermaat',
        'statement_1' => 'Wij vinden dat elk kind en elk gezin zich veilig, zelfstandig en aangenaam met de fiets door hun gemeente moet kunnen verplaatsen. Daarvoor zijn straten en openbare ruimtes nodig die echt rekening houden met de behoeften van kinderen en gezinnen.',
        'statement_2' => 'Via onze parades, onze aanwezigheid in de openbare ruimte en onze dialoog met steden en gemeenten brengen we vier concrete eisen naar voren om onze buurten fietsvriendelijker en aangenamer te maken voor kinderen.',
        'demands' => [
            'title' => 'Waar wij voor pleiten',
            'item_1' => [
                'title' => 'Afgescheiden fietspaden op belangrijke verkeersassen',
                'body' => 'Gezinnen moeten de belangrijkste verkeersassen kunnen gebruiken zonder in het verkeer te moeten vechten voor hun plek. Wij vragen om brede, doorlopende en fysiek van het autoverkeer gescheiden fietspaden, met veilige oversteekplaatsen, in het bijzonder op routes naar scholen en vrijetijdsvoorzieningen.',
            ],
            'item_2' => [
                'title' => 'Fietsenstallingen die aangepast zijn aan gezinnen',
                'body' => 'Je moet je fiets makkelijk kunnen stallen, waar je ook naartoe gaat. Wij vragen meer toegankelijke fietsenstallingen, die ook geschikt zijn voor bakfietsen, longtails en kinderfietsen, en overdekte en goed verlichte stallingen om fietsen ook in het dagelijks leven praktisch te maken.',
            ],
            'item_3' => [
                'title' => 'Echt veilige schoolomgevingen',
                'body' => 'De school zou een van de veiligste plekken voor kinderen moeten zijn. Wij vragen om schoolstraten tijdens de uren waarop kinderen aankomen en vertrekken, veilige schoolomgevingen, duidelijke signalisatie, veilige oversteekplaatsen voor voetgangers en acties die kinderen aanmoedigen om te voet of met de fiets naar school te komen.',
            ],
            'item_4' => [
                'title' => 'Zones 30 die ook echt worden gerespecteerd',
                'body' => 'Een bord met 30 volstaat niet. In woonwijken en rond scholen vragen we om snelheden die ook echt worden gerespecteerd, dankzij controles maar ook door ingrepen die het verkeer vanzelf doen vertragen: smallere straten, verkeersdrempels, verhoogde kruispunten… En sensibilisering van automobilisten over het belang van rustig te rijden op plaatsen waar kinderen wonen en spelen.',
            ],
            'closing' => 'Vier eisen, één doel: kinderen en gezinnen in staat stellen zich veilig met de fiets te verplaatsen. Want een stad waar kinderen zich zelfstandig kunnen verplaatsen, is ook een aangenamere stad voor gezinnen, voetgangers en iedereen die zich anders dan met de auto verplaatst. En zolang dat geen vanzelfsprekendheid is, blijven we hun stem laten horen — op straat, op onze fietsen en in gesprek met de mensen die het verschil kunnen maken.',
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
            'label' => 'Ons manifest',
            'body' => 'Onze volledige visie voor steden op kindermaat, samen met een coalitie van Belgische verenigingen. Ontdek het manifest, deel het en help het verder verspreiden.',
            'link' => 'Download het manifest',
        ],
        'closing' => [
            'heading' => 'Wie houdt de beweging levend?',
            'label' => 'Ontdek hoe we werken',
        ],
    ],

    // Hoe we werken (P-17). Structure: two-paragraph intro (lokaal-eerst +
    // no-HQ; the national/local detail lives in the lists, distill 2026-07-04),
    // two titled lists (nationaal | lokaal), the duo (safety folded into their
    // text), closing to Meehelpen. No bespoke components (variant A).
    'organisation' => [
        'title' => 'Lokale groepen, één community',
        'intro_1' => 'Kidical Mass Belgium is een netwerk van lokale groepen die de beweging in hun gemeente mee tot leven brengen. Elke groep kent zijn buurt, organiseert zijn eigen parades en bouwt banden op met gezinnen, vrijwilligers, verenigingen en lokale organisaties. Een nationale organisatie staat klaar om hen te ondersteunen, zonder de lokale energie ooit over te nemen.',
        'intro_2' => 'Vier keer per jaar komen vrijwilligers samen tijdens regionale meet-ups. We delen ervaringen, goede praktijken en ideeën, nemen samen beslissingen over de beweging en maken vooral tijd om elkaar te ontmoeten. Tussen de bijeenkomsten door houden de coördinatoren contact binnen een netwerk van lokale groepen.',
        'who' => [
            'title' => 'Wie wat doet',
        ],
        'national' => [
            'title' => 'Nationale coördinatie',
            'items' => [
                'Begeleidt bestaande groepen en helpt nieuwe groepen opstarten',
                'Biedt opleidingen aan voor vrijwilligers en begeleiders',
                'Coördineert de nationale communicatie, website, sociale media en persrelaties',
                'Ontwikkelt en stelt Kidical Mass-materiaal ter beschikking, van flyers tot roze hesjes',
                'Ondersteunt groepen in hun contacten met gemeenten, partners en fietsbrigades',
                'Beheert subsidieaanvragen voor het hele netwerk',
                'Waakt over de naleving van het gemeenschappelijke kader rond veiligheid en een goede sfeer',
            ],
        ],
        'local' => [
            'title' => 'Lokale afdelingen',
            'items' => [
                'Organiseren hun eigen parades, met routes en vertrekpunten die zijn aangepast aan hun gemeente',
                'Bereiden de routes voor en mobiliseren vrijwilligers',
                'Verwelkomen en begeleiden gezinnen',
                'Bouwen contacten op met gemeenten, verenigingen en lokale partners',
                'Houden Kidical Mass levend in hun buurt, dicht bij de bewoners',
            ],
        ],
        'duo' => [
            'title' => 'Het coördinatieduo',
            'body_1' => 'Leticia en Cecilia vormen het coördinatieduo van Kidical Mass Belgium. Zij zijn het centrale aanspreekpunt voor lokale groepen en vrijwilligers: ze begeleiden nieuwe teams, beantwoorden dagelijkse vragen, organiseren opleidingen en ondersteunen groepen bij hun communicatie, partnerschappen en organisatie. Leticia staat in voor de algemene coördinatie van de beweging en het financiële en subsidiebeheer. Cecilia houdt zich onder meer bezig met evenementen en de relaties met overheden en partners. Aan hun zijde zorgt Alison voor de communicatie van de beweging: sociale media, content, website en de zichtbaarheid van lokale initiatieven. Want Kidical Mass levend houden betekent ook vertellen wat er op het terrein gebeurt, mensen zin geven om aan de parades deel te nemen en de acties van het netwerk bekendmaken.',
            'body_2' => 'Het team waakt ook over het gemeenschappelijke kader dat ervoor zorgt dat elke parade feestelijk, inclusief en veilig blijft: gedeelde regels, interne communicatie, opleiding van begeleiders, advies over routes, persrelaties, partnerschappen en het zoeken naar financiering. Het doel blijft hetzelfde: lokale groepen de middelen geven om Kidical Mass in hun buurt tot leven te brengen.',
            'link' => 'Hoe een rit praktisch verloopt: Voor het eerst mee →',
        ],
        'parcours' => [
            'title' => 'Routes op kindermaat',
            'body_1' => 'Elke Kidical Mass volgt een nieuwe route, zodat kinderen hun gemeente op een andere manier kunnen ontdekken. De routes lopen zoveel mogelijk langs parken, speelpleinen, scholen of nieuwe fietsinfrastructuur, en vermijden drukke knelpunten en gevaarlijke situaties.',
            'body_2' => 'De fietstochten zijn afgestemd op beginnende fietsers, in het bijzonder kinderen van 3 tot 12 jaar: ongeveer 5 tot 7 km, maximaal een uur, op het tempo van de jongste. Elke parade begint met een korte herinnering aan de verkeersregels en eindigt met een groepsfoto. Ouders blijven tijdens de hele fietstocht verantwoordelijk voor hun kinderen.',
            'body_3' => 'Voor elke parade voorzien we minstens één begeleider in een roze hesje per tien deelnemers, met minstens vier begeleiders per rit. Aan het begin van het seizoen worden opleidingen aangeboden rond begeleiding en het uitstippelen van routes.',
            'link' => 'Hoe een rit praktisch verloopt: Voor het eerst mee',
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
        'title' => 'Het verhaal van de beweging, straat na straat',
        'lead' => 'Nieuws uit de beweging, nieuws van de lokale groepen, projecten, belangrijke mijlpalen en verhalen van de mensen die Kidical Mass overal in België mee tot leven brengen.',
        'empty' => [
            'title' => 'De eerste verhalen komen eraan',
            'body' => 'We bereiden de eerste verhalen voor om met jullie te delen. Kom binnenkort terug of volg ons op :instagram en :facebook om het laatste nieuws van Kidical Mass te ontdekken zodra het er is.',
        ],
        'national' => 'Heel België',
        'gallery' => 'Foto\'s',
        'more' => [
            'title' => 'Meer nieuws',
        ],
        'closing' => [
            'heading' => 'Zin om je bij de beweging aan te sluiten?',
            'label' => 'Vind een rit',
        ],
    ],

    // Pers (P-19). Structure: the year-grouped archive left, a sticky
    // perscontact card right (its label replaces a contact heading). No outlet
    // strip (the archive shows the outlets), no closing CTA (the page IS the
    // contact).
    'press' => [
        'title' => 'Kidical Mass in de kijker',
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
