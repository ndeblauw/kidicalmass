{{--
    Getting Started — "Voor het eerst mee"
    Surface pass 2026-06-04 (Frederik-guided): scroll-stacking card experience.
    - HERO reuses x-page-hero (fixed blue, waving-rider illustration).
    - "Wat je mag verwachten" is a sticky-left + scroll-driven stacking deck on desktop;
      tilted static column on mobile. Section has lead text + 6 big cards with colour-varied
      icon chips. The panel background is light-blue so the rounded seam flows into the sky.
    - FAQ kept as the accordion (contained).
    - CTA is a full-bleed yellow band.
    Structure only; appearance lives in app.css.
--}}
<x-layouts::site title="Voor het eerst mee" :description="__('meta.getting_started')">

    <x-page-hero
        eyebrow="Voor het eerst"
        title="Wat je mag verwachten op een rit"
        illustration="img/illustrations/waving-rider.svg">

    {{-- WAT JE MAG VERWACHTEN — scroll-stacking cards (desktop); static list (mobile) --}}
    <section class="gs-expect-scroll">
        <div class="gs-expect-pin">

            <div class="gs-expect-left">
                <x-intro-text>
                    <p>Elke rit is kort, gratis en veilig. Voor iedereen, zonder voorbereiding. Er is altijd muziek en altijd begeleiders. Je hoeft niets te regelen.</p>
                </x-intro-text>
                @php
                    // Placement (scatter, sizing, FAQ tuck) lives in getting-started.css
                    // so it can adapt per breakpoint; here we only choose the photos.
                    $expectPhotos = [
                        ['src' => 'img/photography/child-yellow-helmet-peace-signs.webp', 'alt' => 'Lachende jongen met een gele helm steekt twee vredestekens op boven zijn stuur tijdens een rit.'],
                        ['src' => 'img/photography/kids-soundbike-flag-obelisk.webp', 'alt' => 'Twee kinderen met hesjes en zonnebril steken hun duim op bij een geluidsfiets onder een grote blauwe vlag.'],
                        ['src' => 'img/photography/ride-girl-pink-jacket-crossing.webp', 'alt' => 'Meisje in een roze jas fietst lachend naar de camera, met twee kinderen naast haar.'],
                    ];
                @endphp
                <x-photo-collage
                    class="gs-expect-collage"
                    :photos="$expectPhotos" />
            </div>

            <div class="gs-expect-right">
                <div class="gs-expect-cards">

                    <x-feature-card class="gs-expect-card" icon="clock" color="red" title="Kort en rustig">
                        5 à 7 km op het tempo van het jongste kind, zelden meer dan een uur.
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="musical-note" color="blue" title="Muziek onderweg">
                        Er is altijd een geluidssysteem. Een vrolijke, luidruchtige fietsparade door de buurt.
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="map-pin" color="orange" title="Vaste startplaats">
                        Elke rit vertrekt op een vaste plek, vermeld op de eventpagina. Gewoon daar opdagen.
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="ticket" color="ink" title="Gratis, geen inschrijving">
                        Geen ticket, geen registratie, geen kosten. Kom gewoon naar de start.
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="users" color="violet" title="Alle leeftijden welkom">
                        Vanaf een jaar of 3, op eigen fiets, in een bakfiets of op een kinderzitje.
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="shield-check" color="coral" title="Minstens vier roze hesjes">
                        Opgeleide begeleiders rijden vooraan en achteraan en houden elke kruising vrij, zodat geen kind achterblijft.
                    </x-feature-card>

                </div>
            </div>

        </div>
    </section>

    {{-- VEELGESTELDE VRAGEN — accordion left + illustration riding in from the right --}}
    <section class="gs-faq-section">
        <div class="gs-faq-layout">

        <div class="gs-faq-content">
        <h2 class="gs-section__title">Veelgestelde vragen</h2>

        <x-faq>
            <x-faq.item question="Moet ik me inschrijven?">
                <p>Nee. Gewoon opdagen op het vertrekpunt op het aangegeven tijdstip. Geen ticket, geen lijst. Je hoeft niets op voorhand te regelen.</p>
            </x-faq.item>
            <x-faq.item question="Vanaf welke leeftijd?">
                <p>Vanaf een jaar of 3. Kinderen rijden op hun eigen fiets (loopfietsen zijn niet geschikt voor de weg), in een bakfiets of op een kinderzitje. Ouders blijven altijd verantwoordelijk voor de veiligheid van hun kind.</p>
            </x-faq.item>
            <x-faq.item question="Moet ik goed kunnen fietsen?">
                <p>Helemaal niet. We rijden op het tempo van het jongste kind, trager dan je denkt. Veel ouders fietsen voor het eerst in het verkeer tijdens een Kidical Mass. Je staat er niet alleen voor, en niemand haast je.</p>
            </x-faq.item>
            <x-faq.item question="Is het veilig in het verkeer?">
                <p>Daar draait alles om. We rijden traag, op kindertempo, met opgeleide begeleiders rond de groep die elke kruising vrijhouden. Waar nodig stemmen de organisatoren de route vooraf af met de lokale politie.</p>
            </x-faq.item>
            <x-faq.item question="Wat als het regent?">
                <p>De rit gaat door bij zowat elk weer. Een beetje regen houdt ons niet tegen. Bij écht extreme omstandigheden wordt het die ochtend aangekondigd op het Facebook-event of de pagina van je afdeling.</p>
            </x-faq.item>
            <x-faq.item question="Wat moeten we meebrengen?">
                <p>Een helm is aangeraden maar niet verplicht. Neem wat water mee. Dat is echt alles. Geen speciale uitrusting, geen voorbereiding nodig.</p>
            </x-faq.item>
            <x-faq.item question="Wat als we geen fiets hebben?">
                <p>Geen fiets is geen reden om thuis te blijven. Soms staat er zelfs een bakfiets klaar aan de start, en op het terrein helpen partners zoals Loopz en My Kids Bikes gezinnen aan een fiets. Vraag er gerust naar bij je lokale groep.</p>
            </x-faq.item>
            <x-faq.item question="Is het echt gratis?">
                <p>Ja, helemaal. Geen inschrijvingsgeld, geen toegangsprijs, geen donatie gevraagd. Kom zoals je bent.</p>
            </x-faq.item>
        </x-faq>
        </div>{{-- /gs-faq-content --}}

        <div class="gs-faq-illustration">
            <img src="{{ asset('img/illustrations/relaxed-rider.svg') }}" alt="" aria-hidden="true" loading="lazy">
        </div>

        </div>{{-- /gs-faq-layout --}}
    </section>

    {{-- Scroll-stacking animation for the expectations cards (lg+ only) --}}
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        if (!window.matchMedia('(min-width: 1024px)').matches) return;

        const section = document.querySelector('.gs-expect-scroll');
        if (!section) return;

        const stack = section.querySelector('.gs-expect-cards');
        const cards = [...stack.querySelectorAll('.gs-expect-card')];
        const N     = cards.length;

        // Final resting position in the stacked deck.
        // Card 0 arrives first (bottom of stack); card N-1 arrives last (top, fully legible).
        const FINALS = [
            { y: 60, r: -1.5 },
            { y: 47, r:  1.0 },
            { y: 34, r: -1.5 },
            { y: 21, r:  1.0 },
            { y: 10, r: -1.5 },
            { y:  0, r:  1.0 },
        ];

        const scrollPerCard = window.innerHeight * 0.1;
        const totalExtra    = N * scrollPerCard;

        section.classList.add('gs-expect-scroll--ready');
        section.style.height = `calc(100dvh + ${totalExtra}px)`;

        // Measure actual card height after layout switches (cards are now position:absolute).
        const cardH   = cards[0].offsetHeight;
        const CARD_GAP = 46; // px, matches the 2.875rem gap in CSS

        // Size the stack to fit the final deck + one card height visible.
        stack.style.height = `${FINALS[0].y + cardH + 20}px`;

        // Last card sits on top of the deck.
        cards.forEach((card, i) => { card.style.zIndex = String(i + 1); });

        const easeOutQuart = t => 1 - Math.pow(1 - t, 4);

        function render() {
            const sectionTop = section.getBoundingClientRect().top + window.pageYOffset;
            const scrolled   = window.pageYOffset - sectionTop;

            cards.forEach((card, i) => {
                const raw = (scrolled - i * scrollPerCard) / scrollPerCard;
                const t   = Math.max(0, Math.min(1, raw));
                const e   = easeOutQuart(t);
                const f   = FINALS[i];

                // Cards start at their natural list positions and fly up to the deck.
                const startY = i * (cardH + CARD_GAP);
                const y      = startY + (f.y - startY) * e;

                card.style.transform = `translateY(${y}px) rotate(${f.r * e}deg)`;
            });
        }

        let raf = null;
        window.addEventListener('scroll', () => {
            if (raf) cancelAnimationFrame(raf);
            raf = requestAnimationFrame(render);
        }, { passive: true });

        render();
    });
    </script>

    {{-- FAQ illustration rides in from the right when it scrolls into view --}}
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const illustration = document.querySelector('.gs-faq-illustration');
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

    <x-slot:closing>
        <x-closing-cta heading="Klaar om mee te rijden?"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>

</x-layouts::site>
