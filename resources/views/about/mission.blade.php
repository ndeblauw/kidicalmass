{{--
    Over ons / Missie — /about/mission (P-15)
    Built 2026-06-03 to the ride/show layout system (DESIGN.md). The first About leaf:
    grounds the "why" for deciders & deepeners (chapter leads, partners, press, proud
    families). Reuses .activity-hero* (blue) + the .activity-promises* idiom (sky band,
    white tilted cards, red Flux-icon chips) for the three axes; stats on a light-blue
    band; inclusivity → Getting Started; a parent pull-quote; closing yellow CTA.
    Colour story: blue → white → sky → light-blue → white → yellow. Structure only;
    appearance in app.css. Plan: docs/wiki/design/30-skeleton/about.md + about-journey.md
--}}
<x-layouts::site title="Missie">

    <x-page-hero
        eyebrow="Missie"
        title="Veilige straten, voor elk kind."
        illustration="img/illustrations/tree-tall.png">

    {{-- WAT KIDICAL MASS IS — contained intro --}}
    <section class="about-intro">
        <p>Kidical Mass Belgium is een nationaal netwerk van lokale groepen die feestelijke, veilige en kindvriendelijke fietsparades organiseren in heel België. We begonnen in 2020 in Brussel en zijn ondertussen actief in meer dan zestien gemeenten in Brussel, Wallonië en Vlaanderen. En we blijven groeien.</p>
        <p>Elke fietsparade heeft muziek onderweg. We rijden op het tempo van het jongste kind, op zorgvuldig gekozen routes, begeleid door getrainde vrijwilligers in opvallende roze hesjes. Kidical Mass is een manier om samen je buurt te ontdekken, nieuwe mensen te leren kennen en zelfvertrouwen op de fiets te winnen. Voor de kinderen, en vaak ook voor de ouders.</p>
    </section>

    {{-- DRIE DINGEN DIE WE DOEN — reuses the promises card idiom on a sky band --}}
    <section class="about-band about-band--sky">
        <div class="container mx-auto px-4">
            <h2 class="about-band__title">Drie dingen die we doen</h2>
            <ul class="about-card-grid" role="list">
                <li>
                    <x-feature-card icon="rocket-launch" color="red" title="Gemeenschappen helpen starten">
                        Elke Kidical Mass begint met een handvol mensen die iets beters willen voor hun buurt. We helpen nieuwe groepen een lokale fietsparade op te starten, van de eerste vergadering tot de eerste rit.
                    </x-feature-card>
                </li>
                <li>
                    <x-feature-card icon="lifebuoy" color="red" title="Bestaande groepen ondersteunen">
                        Lokale groepen staan er niet alleen voor. We bieden vorming, coördinatiemiddelen, materiaal en nationale zichtbaarheid, zodat elke groep zich kan richten op wat telt: mensen samenbrengen.
                    </x-feature-card>
                </li>
                <li>
                    <x-feature-card icon="megaphone" color="red" title="Pleiten voor kindvriendelijke straten">
                        Vrolijke fietsparades zijn een begin, geen eindpunt. We werken samen met steden en regio's voor veiligere infrastructuur, trager verkeer en straten die kinderen en gezinnen echt verwelkomen. <a href="{{ route('about.vision') }}">Lees onze visie →</a>
                    </x-feature-card>
                </li>
            </ul>
        </div>
    </section>

    {{-- IMPACT IN CIJFERS — full-bleed light-blue band --}}
    <section class="about-stats" aria-label="Impact in cijfers">
        <div class="container mx-auto px-4">
            <ul class="about-stats__grid" role="list">
                <li class="about-stat"><span class="about-stat__num">150+</span><span class="about-stat__label">fietsparades sinds 2020</span></li>
                <li class="about-stat"><span class="about-stat__num">5.500+</span><span class="about-stat__label">deelnemers</span></li>
                <li class="about-stat"><span class="about-stat__num">120</span><span class="about-stat__label">actieve vrijwilligers</span></li>
                <li class="about-stat"><span class="about-stat__num">16+</span><span class="about-stat__label">gemeenten in heel België</span></li>
            </ul>
        </div>
    </section>

    {{-- STEUN — contextual ask at the peak-intent moment, right after the impact stats --}}
    <x-support-callout
        title="Al onze ritten zijn gratis"
        body="Voor elk gezin, in elke buurt. Jouw steun zorgt dat dat zo blijft, en dat er nieuwe buurten bijkomen." />

    {{-- IEDEREEN IS WELKOM — contained --}}
    <section class="about-section">
        <h2 class="about-section__title">Iedereen is welkom</h2>
        <p>Je hoeft geen ervaren fietser te zijn. Nog nooit in het verkeer gefietst? Dat geeft niets. Voor veel ouders is een rit de eerste keer op de baan, en onze begeleiders zorgen dat niemand er alleen voor staat. Je hoeft geen fiets te hebben. Je hoeft niet uit de buurt te komen. Kidical Mass is gemaakt om de volledige diversiteit van elke gemeente te weerspiegelen, en om elke drempel weg te nemen die een gezin kan tegenhouden.</p>
        <p class="about-section__link"><a href="{{ route('getting-started') }}">Geen fiets of nog nooit meegereden? Voor het eerst mee →</a></p>
    </section>

    {{-- OUDER AAN HET WOORD — pull-quote --}}
    <figure class="about-quote">
        <blockquote>
            <p>“Wat hij zo leuk vindt aan fietsen, denk ik, is die vrijheid om buiten te zijn, lucht te hebben, er alleen op uit te trekken. Hij wil altijd ver gaan, iets nieuws ontdekken.”</p>
        </blockquote>
        <figcaption>Julienne, mama van twee kinderen (2 en 5 jaar)</figcaption>
    </figure>

    @push('scripts')
    <x-about-reveal selector=".about-band .about-card-grid > li" />
    @endpush

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta heading="Samen maken we straten veiliger"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>

</x-layouts::site>
