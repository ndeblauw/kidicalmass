{{--
    Over ons / Visie — /about/vision (P-16)
    Built 2026-06-03 to the DESIGN.md kit. The advocacy leaf: what Kidical Mass is
    fighting for. Stronger register than the event pages, still not preachy (ToV).
    Reuses .activity-hero* (blue) + a numbered demand grid on a light-blue band;
    manifesto link + parent voices on white; shared closing CTA.
    Colour story: blue → white → light-blue → white → yellow. Structure only.
    Merges the legacy /nos-revendications + /what-we-want pages.
    Plan: docs/wiki/design/30-skeleton/about.md + about-journey.md
--}}
<x-layouts::site title="Visie">

    <x-page-hero
        eyebrow="Visie"
        title="Een stad op kindermaat."
        illustration="img/illustrations/bird-with-helmet.png">

    {{-- POSITIESTATEMENT — contained intro --}}
    <x-intro-text size="lead">
        <p>Kidical Mass begon als een fietsparade. Het werd een beweging. En bewegingen vieren niet alleen wat mogelijk is, ze vragen erom.</p>
        <p>We geloven dat elk kind in België zich veilig en met vertrouwen door zijn stad moet kunnen bewegen. Dat straten ontworpen horen te zijn voor de mensen die er wonen, niet alleen voor de auto's die er passeren. Dat kinderen mee mogen beslissen over hoe hun buurt eruitziet.</p>
        <p>Dat is niet radicaal. Het is wat de meeste ouders willen. Het is wat onderzoek bevestigt. En het is waar we naartoe werken: één rit, één gemeenteraad, één beleidsgesprek tegelijk.</p>
    </x-intro-text>

    {{-- VIER EISEN — numbered demand grid on a light-blue band --}}
    <section class="about-band about-band--light-blue">
        <div class="container mx-auto px-4">
            <h2 class="about-band__title">Wat we vragen</h2>
            <ol class="about-demand-grid">
                <x-numbered-item number="1" title="Veilige fietsinfrastructuur voor kinderen en gezinnen">
                    Aparte fietspaden die kinderen echt kunnen gebruiken: gescheiden van het verkeer, goed onderhouden en aaneengesloten. Gebouwd voor de kleinste fietsers, niet alleen voor de snelste.
                </x-numbered-item>
                <x-numbered-item number="2" title="Tragere, rustigere woonstraten">
                    Minder snel en minder druk verkeer in de straten waar kinderen wonen en spelen. Twintig is genoeg, en handhaving telt evenveel als borden.
                </x-numbered-item>
                <x-numbered-item number="3" title="Openbare ruimte die kinderen en gezinnen echt verwelkomt">
                    Parken, pleinen en straten waar kinderen kind kunnen zijn: luidruchtig, nieuwsgierig, in beweging. Ruimte die werkt voor kinderwagens en bakfietsen, niet alleen voor auto's en gehaaste volwassenen.
                </x-numbered-item>
                <x-numbered-item number="4" title="De stem van kinderen in beslissingen over hun omgeving">
                    Kinderen zijn experts van hun eigen buurt. Ze verdienen echte inspraak, geen symbolisch gebaar, wanneer steden straten, parken en openbare ruimte plannen.
                </x-numbered-item>
            </ol>
        </div>
    </section>

    {{-- MANIFEST + OUDERS AAN HET WOORD — contained white --}}
    <section class="about-section">
        <x-section-heading>Lees het manifest</x-section-heading>
        <p>We zetten onze volledige visie op papier. Lees het manifest, mee ondertekend door een coalitie van Belgische verenigingen, en deel het.</p>
        {{-- NB: legacy Wix-gehoste PDF, moet herhost worden (zie D-7 / about-journey.md). --}}
        <p class="about-section__link"><a href="https://www.kidicalmass.be/_files/ugd/cf0153_2b074cb919ea46698c1732a2f55b26eb.pdf" target="_blank" rel="noopener noreferrer">Download het manifest (PDF) →</a></p>

        {{-- Oudergetuigenissen, RIEPP-studie 2021, met toestemming. NL-vertaling
             ter bevestiging bij het coördinatieduo (zie about-journey.md). --}}
        <div class=”about-voices”>
            <x-pull-quote variant=”card” attribution=”Camille, mama van twee kinderen, Sint-Gillis”>
                “Ik heb het gevoel dat ik de hele tijd de levenslust van mijn kinderen afrem.”
            </x-pull-quote>
            <x-pull-quote variant=”card” attribution=”Fatima, mama van drie kinderen, Jette”>
                “Ik ben constant bang voor de auto's, de trams. Tegen dat we thuis zijn van school, ben ik uitgeput.”
            </x-pull-quote>
        </div>
    </section>

    @push('scripts')
    <x-about-reveal selector=".about-demand" :transform="true" />
    @endpush

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta heading="Geloof je hierin?"
            :href="route('membership')" label="Word lid" icon="heart" />
    </x-slot:closing>

</x-layouts::site>
