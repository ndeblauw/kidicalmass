{{--
    Een lokale groep starten (P-?? — new, planned 2026-06-15)
    The canonical "start a local group" page. Replaces the mailto:bike@ coda on Help
    out + the CTA on Chapters (D-12 "email black hole"). Strategy: dissolve three
    barriers — "te groot een klus" (de deal), "wie steunt mij?" (je staat er niet
    alleen voor + the warm form), "is er animo?" (er is animo / proof) — while being
    honest about the commitment so the team gets fewer, higher-intent leads.
    One intent form, two comfort paths (praten met een trekker / klaar voor contact).
    Out of nav, reached contextually. Plan: docs/wiki/design/30-skeleton/start-een-groep.md
--}}
<x-layouts::site title="Een lokale groep starten">

    <x-page-hero
        eyebrow="Een lokale groep starten"
        title="Breng Kidical Mass naar jouw buurt"
        photo="img/photography/team-kidical-mass.webp"
        photo-alt="Vier vrijwilligers van het Kidical Mass team lachen met roze hesjes en blauwe vlaggen, naast een kartonnen figuur van twee kinderen op de fiets"
        caption="Foto © Marc Baert">

        {{-- Intro opens the white panel, with the "start" CTA in a right column,
             vertically centred to the intro copy (stacks below it on mobile). --}}
        <div class="sg-intro">
            <x-intro-text>
                <p>Je hebt geen vereniging nodig en je hoeft geen fietsexpert te zijn. Een klein kernteam,
                een vertrekpunt en wat goesting volstaan om te beginnen. De rest doen we samen.</p>
            </x-intro-text>
            <div class="sg-intro__action">
                <x-cta-button href="#start" variant="secondary">Ik wil starten</x-cta-button>
            </div>
        </div>

        {{-- DRIE SECTIES — simple alternating two-column lockups (collage + text),
             vertically centred, height driven by the content. Order alternates:
             collage-text, text-collage, collage-text. No sticky, no crossfade. --}}
        <section class="sg-story">
            <div class="sg-story__row">
                <div class="sg-story__collage sg-story__collage--a">
                    <figure class="sg-story__photo sg-story__photo--lead">
                        <img src="{{ asset('img/photography/ride-brussels-two-boys-at-start.webp') }}"
                             alt="Twee jongens arm in arm met hun fietsen en groene helmen aan de start van een rit in Brussel" loading="lazy">
                    </figure>
                    <figure class="sg-story__photo sg-story__photo--trail">
                        <img src="{{ asset('img/photography/cargo-bike-mother-two-kids-flag.webp') }}"
                             alt="Een glimlachende vrouw rijdt op een cargobike met twee kinderen en een Kidical Mass vlag" loading="lazy">
                    </figure>
                </div>
                <div class="sg-story__text">
                    <x-titled-list-block title="Jij hebt al wat nodig is" variant="ask" level="h2">
                        <li>Een kernteam van twee of drie mensen</li>
                        <li>Kennis van je eigen buurt</li>
                        <li>Een vertrekpunt en een route-idee</li>
                        <li>Energie en goesting</li>
                    </x-titled-list-block>
                </div>
            </div>

            <div class="sg-story__row sg-story__row--reverse">
                <div class="sg-story__collage sg-story__collage--b">
                    <figure class="sg-story__photo sg-story__photo--lead">
                        <img src="{{ asset('img/photography/ride-group-celebration-station.webp') }}"
                             alt="Tientallen gezinnen in fluohesjes juichen met opgeheven armen voor een sierlijk bakstenen station" loading="lazy">
                    </figure>
                    <figure class="sg-story__photo sg-story__photo--trail">
                        <img src="{{ asset('img/photography/ride-girl-smiling-on-bike.webp') }}"
                             alt="Een lachend meisje in een roze helm rijdt mee in een groep" loading="lazy">
                    </figure>
                </div>
                <div class="sg-story__text">
                    <x-titled-list-block title="De rest doen we samen" variant="get" level="h2">
                        <li>Het merk en al het materiaal, van flyers tot hesjes</li>
                        <li>Opleiding rond veilige begeleiding en routeplanning</li>
                        <li>Nationale zichtbaarheid en communicatie</li>
                        <li>Coaching en een vast aanspreekpunt</li>
                        <li>Contacten met gemeenten, partners en fietsbrigades</li>
                        <li>Subsidieaanvragen voor de hele organisatie</li>
                    </x-titled-list-block>
                </div>
            </div>

            <div class="sg-story__row">
                <div class="sg-story__collage sg-story__collage--c">
                    <figure class="sg-story__photo sg-story__photo--lead">
                        <img src="{{ asset('img/photography/ride-trio-pink-vest-lei-portrait.webp') }}"
                             alt="Drie vrijwilligers lachen samen tijdens een rit, één met een roze hesje en een bloemenkrans" loading="lazy">
                    </figure>
                    <figure class="sg-story__photo sg-story__photo--trail">
                        <img src="{{ asset('img/photography/ride-brussels-boulevard-crowd.webp') }}"
                             alt="Een dichte menigte gezinnen met fietsen op een zonnige Brusselse boulevard" loading="lazy">
                    </figure>
                </div>
                <div class="sg-story__text">
                    <div class="titled-list-block titled-list-block--ask">
                        <h2 class="titled-list-block__title">Waar je ja tegen zegt</h2>
                        <p class="sg-asks__lead">Eerlijk is eerlijk: een groep dragen is een engagement over een
                        heel seizoen. Dit verwachten we van een lokale trekker.</p>
                        <ul class="sg-asks__list" role="list">
                            <li>Een paar ritten per jaar mee plannen en begeleiden</li>
                            <li>Eén afgevaardigde naar de vier jaarlijkse Kidical-meetings</li>
                            <li>Je scharen achter ons huishoudelijk reglement rond veiligheid en goede vibes</li>
                            <li>Genoeg begeleiders verzamelen: minstens één roze hesje per tien deelnemers</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- ER IS ANIMO — proof the movement exists: the wide crowd photo beside the
             light-blue animo card. Sits before the FAQ so the visual proof frames the
             practical questions. --}}
        <section class="sg-proof">
            <div class="sg-proof__layout">
                <figure class="sg-proof__photo">
                    <img src="{{ asset('img/photography/ride-park-crowd-cheering-namur.webp') }}"
                         alt="Een grote menigte gezinnen juicht met opgeheven armen op een zonnige verzamelplaats in Namen"
                         loading="lazy">
                </figure>
                <div class="sg-proof__animo-card">
                    <h2>Er is animo</h2>
                    <p>Kidical Mass groeit door heel België. Het netwerk telt intussen
                    {{ $groupCount }} lokale groepen, van grote steden tot kleine gemeenten.
                    Jouw stad kan de volgende zijn.</p>
                    <x-cta-button href="#start" variant="secondary">Ik wil starten</x-cta-button>
                </div>
            </div>
        </section>

        {{-- VEELGESTELDE VRAGEN — practical lead objections, after the visual proof.
             Full-bleed with the illustration sliding in from the right, matching the
             getting-started FAQ pattern. --}}
        <section class="sg-faq-section">
            <div class="sg-faq-layout">
                <div class="sg-faq-content">
                    <h2 class="sg-faq__title">Veelgestelde vragen</h2>
                    <x-faq>
                        <x-faq.item question="Welke steun krijg ik van Kidical Mass?">
                            <p>Je staat er nooit alleen voor. Je krijgt een coördinatieduo dat je coacht en
                            motiveert, een materiaalbibliotheek met charters, draaiboeken, posters en flyers,
                            en training voor jou en je begeleiders bij de start van het seizoen. Wil je sparren,
                            dan brengen we je in contact met een trekker die het al deed. En wij dragen het merk,
                            de opleiding rond veilige begeleiding, de nationale zichtbaarheid, de contacten met
                            gemeenten en partners, en de subsidieaanvragen voor de hele organisatie.</p>
                        </x-faq.item>
                        <x-faq.item question="Heb ik een vereniging of vzw nodig?">
                            <p>Nee. Een klein kernteam van twee of drie mensen, een vertrekpunt en wat goesting
                            volstaan om te beginnen. De rest doen we samen.</p>
                        </x-faq.item>
                        <x-faq.item question="Moet ik een ervaren fietser zijn?">
                            <p>Geen fietsexpert nodig. We rijden traag, op het tempo van het jongste kind. Wat telt
                            is dat je je buurt kent en mensen warm krijgt om mee te fietsen. De opleiding rond
                            veilige begeleiding en routeplanning krijg je van ons.</p>
                        </x-faq.item>
                        <x-faq.item question="Kan ik starten als ik nog geen team heb?">
                            <p>Veel groepen starten klein, met één of twee enthousiastelingen. Je hoeft niet meteen
                            een volledig team te hebben. Twijfel je, kies dan hieronder voor "eerst praten met
                            iemand die het al deed", dan zoeken we samen verder.</p>
                        </x-faq.item>
                    </x-faq>
                </div>

                <div class="sg-faq-illustration">
                    <img src="{{ asset('img/illustrations/cargo-bike-family.svg') }}" alt="" aria-hidden="true" loading="lazy">
                </div>
            </div>
        </section>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const illustration = document.querySelector('.sg-faq-illustration');
        if (!illustration) return;

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            illustration.classList.add('is-in');
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    illustration.classList.add('is-in');
                    observer.disconnect();
                }
            });
        }, { threshold: 0.25 });

        observer.observe(illustration);
    });
    </script>
    @endpush

    </x-page-hero>

    {{-- INTENT-FORM — the climax. A white form card on the yellow closing band, with
         room around it (no dip). Two columns at desktop: welcome text left, form right.
         The #start anchor catches the hero's "Ik wil starten". --}}
    <x-slot:closing>
        <section class="sg-cta" id="start">
            <div class="container mx-auto px-4">
                <div class="sg-cta__panel">
                    <div class="sg-cta__aside">
                        <h2>Zin om te beginnen?</h2>
                        <p>Laat van je horen. Je kiest zelf hoe je eerste stap eruitziet:
                        eerst praten met iemand die het al deed, of meteen contact met het team.</p>
                    </div>
                    <div class="sg-cta__form-col">
                        <livewire:start-group-enquiry />
                    </div>
                </div>
                <p class="sg-cta__secondary">Liever eerst rondkijken?
                    <a href="{{ route('groups.index') }}">Bekijk alle groepen</a></p>
            </div>
        </section>
    </x-slot:closing>

</x-layouts::site>
