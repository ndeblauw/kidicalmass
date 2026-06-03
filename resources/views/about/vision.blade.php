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

    {{-- HERO --}}
    <section class="activity-hero about-hero">
        <img src="{{ asset('img/logo-icon.png') }}" alt="" aria-hidden="true" class="activity-hero__daisy">
        <div class="container mx-auto px-4 activity-hero__inner">
            <div class="activity-hero__copy">
                <h1>Visie</h1>
                <p class="about-hero__lead">We willen straten waar kinderen vrij kunnen fietsen. België is er nog niet. Maar we weten wat daarvoor nodig is.</p>
            </div>
            <div class="activity-hero__visual">
                <div class="activity-hero__photo about-hero__circle">
                    <img src="{{ asset('img/illustrations/kid-on-bike.png') }}" alt="" aria-hidden="true">
                </div>
            </div>
        </div>
    </section>

    {{-- POSITIESTATEMENT — contained intro --}}
    <section class="about-intro about-intro--lead">
        <p>Kidical Mass begon als een fietsparade. Het werd een beweging. En bewegingen vieren niet alleen wat mogelijk is, ze vragen erom.</p>
        <p>We geloven dat elk kind in België zich veilig en met vertrouwen door zijn stad moet kunnen bewegen. Dat straten ontworpen horen te zijn voor de mensen die er wonen, niet alleen voor de auto's die er passeren. Dat kinderen mee mogen beslissen over hoe hun buurt eruitziet.</p>
        <p>Dat is niet radicaal. Het is wat de meeste ouders willen. Het is wat onderzoek bevestigt. En het is waar we naartoe werken: één rit, één gemeenteraad, één beleidsgesprek tegelijk.</p>
    </section>

    {{-- VIER EISEN — numbered demand grid on a light-blue band --}}
    <section class="about-band about-band--light-blue">
        <div class="container mx-auto px-4">
            <h2 class="about-band__title">Wat we vragen</h2>
            <ol class="about-demand-grid">
                <li class="about-demand">
                    <span class="about-demand__num" aria-hidden="true">1</span>
                    <strong>Veilige fietsinfrastructuur voor kinderen en gezinnen</strong>
                    <p>Aparte fietspaden die kinderen echt kunnen gebruiken: gescheiden van het verkeer, goed onderhouden en aaneengesloten. Gebouwd voor de kleinste fietsers, niet alleen voor de snelste.</p>
                </li>
                <li class="about-demand">
                    <span class="about-demand__num" aria-hidden="true">2</span>
                    <strong>Tragere, rustigere woonstraten</strong>
                    <p>Minder snel en minder druk verkeer in de straten waar kinderen wonen en spelen. Twintig is genoeg, en handhaving telt evenveel als borden.</p>
                </li>
                <li class="about-demand">
                    <span class="about-demand__num" aria-hidden="true">3</span>
                    <strong>Openbare ruimte die kinderen en gezinnen echt verwelkomt</strong>
                    <p>Parken, pleinen en straten waar kinderen kind kunnen zijn: luidruchtig, nieuwsgierig, in beweging. Ruimte die werkt voor kinderwagens en bakfietsen, niet alleen voor auto's en gehaaste volwassenen.</p>
                </li>
                <li class="about-demand">
                    <span class="about-demand__num" aria-hidden="true">4</span>
                    <strong>De stem van kinderen in beslissingen over hun omgeving</strong>
                    <p>Kinderen zijn experts van hun eigen buurt. Ze verdienen echte inspraak, geen symbolisch gebaar, wanneer steden straten, parken en openbare ruimte plannen.</p>
                </li>
            </ol>
        </div>
    </section>

    {{-- MANIFEST + OUDERS AAN HET WOORD — contained white --}}
    <section class="about-section">
        <h2 class="about-section__title">Lees het manifest</h2>
        <p>We zetten onze volledige visie op papier. Lees het manifest, mee ondertekend door een coalitie van Belgische verenigingen, en deel het.</p>
        {{-- NB: legacy Wix-gehoste PDF, moet herhost worden (zie D-7 / about-journey.md). --}}
        <p class="about-section__link"><a href="https://www.kidicalmass.be/_files/ugd/cf0153_2b074cb919ea46698c1732a2f55b26eb.pdf" target="_blank" rel="noopener noreferrer">Download het manifest (PDF) →</a></p>

        {{-- Oudergetuigenissen, RIEPP-studie 2021, met toestemming. NL-vertaling
             ter bevestiging bij het coördinatieduo (zie about-journey.md). --}}
        <div class="about-voices">
            <figure class="about-voice">
                <blockquote><p>“Ik heb het gevoel dat ik de hele tijd de levenslust van mijn kinderen afrem.”</p></blockquote>
                <figcaption>Camille, mama van twee kinderen, Sint-Gillis</figcaption>
            </figure>
            <figure class="about-voice">
                <blockquote><p>“Ik ben constant bang voor de auto's, de trams. Tegen dat we thuis zijn van school, ben ik uitgeput.”</p></blockquote>
                <figcaption>Fatima, mama van drie kinderen, Jette</figcaption>
            </figure>
        </div>
    </section>

    {{-- CTA --}}
    <x-about-cta
        title="Doe mee met de beweging"
        sub="Elke rit is ook een vraag aan de stad. Help mee, of steun de beweging zodat ze kan blijven groeien.">
        <x-slot:actions>
            <a href="{{ route('volunteer') }}" class="about-cta__btn about-cta__btn--primary link-plain">Help mee →</a>
            <a href="{{ route('membership') }}" class="about-cta__btn about-cta__btn--ghost link-plain">Steun Kidical Mass →</a>
        </x-slot:actions>
    </x-about-cta>

    @push('scripts')
    <x-about-reveal selector=".about-demand" :transform="true" />
    @endpush

</x-layouts::site>
