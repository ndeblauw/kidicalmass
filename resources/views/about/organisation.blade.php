{{--
    Over ons / Organisatie — /about/organisation (P-17)
    Built 2026-06-03 to the DESIGN.md kit. Makes the federated, volunteer-run structure
    legible to deciders (chapter leads, partners, funders, press). Reuses .activity-hero*
    (blue) + the .ho-deal* two-column idiom (sky band) for national-vs-local; a lightweight
    semantic organigram; the named coördinatieduo (Leticia & Cecilia, bios/photos pending);
    a safety note → Getting Started; shared closing CTA routed to start/join a chapter.
    Colour story: blue → white → white(organigram) → sky → white → yellow. Structure only.
    Plan: docs/wiki/design/30-skeleton/about.md + about-journey.md
--}}
<x-layouts::site title="Organisatie">

    <x-page-hero
        eyebrow="Organisatie"
        title="Buren die de straat op trekken."
        illustration="img/illustrations/kid-on-bike-teal.png">

    {{-- HOE WE GEORGANISEERD ZIJN — contained intro --}}
    <x-intro-text>
        <p>Kidical Mass Belgium is zo opgebouwd dat het overal echt lokaal blijft. Geen nationale campagne met lokale filialen, maar een netwerk van groepen die elk hun eigen buurt kennen.</p>
        <p>Op nationaal niveau doet een klein coördinatieteam wat alleen op dat niveau kan: het merk bewaken, vorming ontwikkelen, communicatie en partnerschappen coördineren en subsidies aanvragen voor het hele netwerk. Het team schept de voorwaarden waarin lokale groepen kunnen groeien. Het stuurt ze niet aan.</p>
        <p>Op lokaal niveau is elke afdeling autonoom. Lokale trekkers kiezen hun eigen routes, verzamelpunten en partners. Zij kennen hun buurt beter dan wie ook. Die autonomie is bewust, en niet onderhandelbaar.</p>
    </x-intro-text>

    {{-- ORGANIGRAM — lightweight semantic 3-tier flow --}}
    <section class="about-section about-section--wide">
        <h2 class="about-section__title">Hoe het in elkaar zit</h2>
        <ol class="about-organigram" aria-label="Structuur van Kidical Mass Belgium, van lokaal naar nationaal">
            <li class="about-organigram__node about-organigram__node--lead">
                <span class="about-organigram__role">Nationaal</span>
                <strong>Coördinatieduo</strong>
                <span class="about-organigram__note">Ondersteunt, verbindt en vertegenwoordigt het netwerk.</span>
            </li>
            <li class="about-organigram__node">
                <span class="about-organigram__role">Vier keer per jaar</span>
                <strong>Regionale ontmoetingen</strong>
                <span class="about-organigram__note">Trekkers wisselen ervaringen uit en stemmen acties op elkaar af.</span>
            </li>
            <li class="about-organigram__node about-organigram__node--base">
                <span class="about-organigram__role">Lokaal</span>
                <strong>16+ lokale afdelingen</strong>
                <span class="about-organigram__note">Elke afdeling organiseert autonoom haar eigen ritten.</span>
            </li>
        </ol>
    </section>

    {{-- WIE WAT DOET — two honest columns, reuses the ho-deal idiom on a sky band --}}
    <section class="ho-deal about-deal">
        <div class="container mx-auto px-4">
            <h2 class="ho-deal__title">Wie wat doet</h2>
            <div class="ho-deal__cols">
                <div class="ho-deal__col">
                    <h3>Nationale coördinatie</h3>
                    <ul class="ho-deal__list" role="list">
                        <li>Bewaakt het merk en de identiteit van Kidical Mass Belgium</li>
                        <li>Ontwikkelt vorming en onboarding voor nieuwe trekkers</li>
                        <li>Coördineert nationale communicatie, website en pers</li>
                        <li>Beheert partnerschappen en institutionele relaties</li>
                        <li>Dient subsidieaanvragen in voor het hele netwerk</li>
                        <li>Verbindt groepen, zodat niemand een opgelost probleem opnieuw moet oplossen</li>
                    </ul>
                </div>
                <div class="ho-deal__col">
                    <h3>Lokale afdelingen</h3>
                    <ul class="ho-deal__list" role="list">
                        <li>Organiseren hun eigen fietsparades</li>
                        <li>Kiezen routes en verzamelpunten</li>
                        <li>Werven en begeleiden lokale vrijwilligers</li>
                        <li>Bouwen banden met lokale partners en de gemeente</li>
                        <li>Kennen hun buurt en de gezinnen erin</li>
                        <li>Zíjn de beweging. De coördinatie bestaat om hen te steunen, niet andersom.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- STEUN — contextual ask right after the "we run on nothing" money model --}}
    <x-support-callout
        title="Geen hoofdkantoor, geen betaald personeel"
        body="Kidical Mass draait op vrijwilligers en op de steun van mensen zoals jij. Zo blijven we onafhankelijk en gratis." />

    {{-- COÖRDINATIEDUO — named --}}
    <section class="about-section">
        <h2 class="about-section__title">Het coördinatieduo</h2>
        <p>Leticia en Cecilia vormen samen het coördinatieduo. Zij zijn het centrale aanspreekpunt voor lokale groepen en vrijwilligers: ze organiseren vorming voor veilige begeleiding, lossen dagelijkse vragen op en bewaken de basiskwaliteit en veiligheid van elke rit. Een dienende rol, geen hiërarchie.</p>
        {{-- Foto's + persoonlijke bio's nog aan te leveren door het duo (zie about-journey.md). --}}
        <ul class="about-duo" role="list">
            <li class="about-duo__person"><span class="about-duo__name">Leticia</span><span class="about-duo__role">Coördinatie</span></li>
            <li class="about-duo__person"><span class="about-duo__name">Cecilia</span><span class="about-duo__role">Coördinatie</span></li>
        </ul>
    </section>

    {{-- VEILIGHEID EN ROUTES --}}
    <section class="about-section">
        <h2 class="about-section__title">Veiligheid en routes</h2>
        <p>Alle afdelingen werken met dezelfde veiligheidsafspraken en routerichtlijnen. Elke route loopt langs parken, speelpleinen en veilige infrastructuur. Begeleiders krijgen bij de start van elk seizoen vorming, en waar nodig stemmen organisatoren de route vooraf af met de lokale politie.</p>
        <p class="about-section__link"><a href="{{ route('getting-started') }}">Hoe een rit praktisch verloopt: Voor het eerst mee →</a></p>
    </section>

    @push('scripts')
    <x-about-reveal selector=".about-organigram__node" :transform="true" />
    @endpush

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta heading="Een afdeling starten of vervoegen?"
            :href="route('getting-started')" label="Zo begin je" />
    </x-slot:closing>

</x-layouts::site>
