{{--
    Getting Started — "Voor het eerst mee"
    Surface pass 2026-06-04 (Frederik-guided): scroll-stacking card experience.
    - HERO reuses x-page-hero (fixed blue, kid-on-bike illustration).
    - "Wat je mag verwachten" is a sticky-left + scroll-driven stacking deck on desktop;
      tilted static column on mobile. Section has lead text + 6 big cards with colour-varied
      icon chips. The panel background is light-blue so the rounded seam flows into the sky.
    - FAQ kept as the accordion (contained).
    - CTA is a full-bleed yellow band.
    Structure only; appearance lives in app.css.
--}}
<x-layouts::site title="Voor het eerst mee">

    <x-page-hero
        eyebrow="Voor het eerst"
        title="Wat je mag verwachten op een rit"
        illustration="img/illustrations/kid-on-bike.png">

    {{-- WAT JE MAG VERWACHTEN — scroll-stacking cards (desktop); static list (mobile) --}}
    <section class="gs-expect-scroll">
        <div class="gs-expect-pin">

            <div class="gs-expect-left">
                <p class="mb-12">Elke rit is kort, gratis en veilig. Voor iedereen, zonder voorbereiding. Er is altijd muziek en altijd begeleiders. Je hoeft niets te regelen.</p>
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

    {{-- VEELGESTELDE VRAGEN — illustration left + accordion right --}}
    <section class="gs-faq-section">
        <div class="gs-faq-layout">

        <div class="gs-faq-illustration">
            <img src="{{ asset('img/illustrations/kid-on-scooter.png') }}" alt="" aria-hidden="true" loading="lazy">
        </div>

        <div class="gs-faq-content">
        <h2 class="gs-section__title">Veelgestelde vragen</h2>

        <div class="gs-faq">
            <details class="gs-faq__item">
                <summary class="gs-faq__q">Moet ik me inschrijven?</summary>
                <div class="gs-faq__a">
                    <p>Nee. Gewoon opdagen op het vertrekpunt op het aangegeven tijdstip. Geen ticket, geen lijst. Je hoeft niets op voorhand te regelen.</p>
                </div>
            </details>
            <details class="gs-faq__item">
                <summary class="gs-faq__q">Vanaf welke leeftijd?</summary>
                <div class="gs-faq__a">
                    <p>Vanaf een jaar of 3. Kinderen rijden op hun eigen fiets (loopfietsen zijn niet geschikt voor de weg), in een bakfiets of op een kinderzitje. Ouders blijven altijd verantwoordelijk voor de veiligheid van hun kind.</p>
                </div>
            </details>
            <details class="gs-faq__item">
                <summary class="gs-faq__q">Moet ik goed kunnen fietsen?</summary>
                <div class="gs-faq__a">
                    <p>Helemaal niet. We rijden op het tempo van het jongste kind, trager dan je denkt. Veel ouders fietsen voor het eerst in het verkeer tijdens een Kidical Mass. Je staat er niet alleen voor, en niemand haast je.</p>
                </div>
            </details>
            <details class="gs-faq__item">
                <summary class="gs-faq__q">Is het veilig in het verkeer?</summary>
                <div class="gs-faq__a">
                    <p>Daar draait alles om. We rijden traag, op kindertempo, met opgeleide begeleiders rond de groep die elke kruising vrijhouden. Waar nodig stemmen de organisatoren de route vooraf af met de lokale politie.</p>
                </div>
            </details>
            <details class="gs-faq__item">
                <summary class="gs-faq__q">Wat als het regent?</summary>
                <div class="gs-faq__a">
                    <p>De rit gaat door bij zowat elk weer. Een beetje regen houdt ons niet tegen. Bij écht extreme omstandigheden wordt het die ochtend aangekondigd op het Facebook-event of de pagina van je afdeling.</p>
                </div>
            </details>
            <details class="gs-faq__item">
                <summary class="gs-faq__q">Wat moeten we meebrengen?</summary>
                <div class="gs-faq__a">
                    <p>Een helm is aangeraden maar niet verplicht. Neem wat water mee. Dat is echt alles. Geen speciale uitrusting, geen voorbereiding nodig.</p>
                </div>
            </details>
            <details class="gs-faq__item">
                <summary class="gs-faq__q">Wat als we geen fiets hebben?</summary>
                <div class="gs-faq__a">
                    <p>Geen fiets is geen reden om thuis te blijven. Soms staat er zelfs een bakfiets klaar aan de start, en er zijn verschillende manieren om er een te lenen of te huren. <a href="{{ route('find-a-bike') }}">Bekijk de opties →</a></p>
                </div>
            </details>
            <details class="gs-faq__item">
                <summary class="gs-faq__q">Is het echt gratis?</summary>
                <div class="gs-faq__a">
                    <p>Ja, helemaal. Geen inschrijvingsgeld, geen toegangsprijs, geen donatie gevraagd. Kom zoals je bent.</p>
                </div>
            </details>
        </div>
        </div>{{-- /gs-faq-content --}}

        </div>{{-- /gs-faq-layout --}}
    </section>

    {{-- PRIMARY CTA — full-bleed yellow band --}}
    <section class="gs-cta">
        <div class="container mx-auto px-4 gs-cta__inner">
            <h2>Klaar voor je eerste rit?</h2>
            <p class="gs-cta__sub">Zoek een rit bij jou in de buurt en kom gewoon langs.</p>
            <x-cta-button :href="route('activities.index')" variant="blue" class="link-plain">Vind een rit bij jou in de buurt</x-cta-button>
        </div>
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

        const scrollPerCard = window.innerHeight * 0.35;
        const totalExtra    = N * scrollPerCard;

        section.style.height = `calc(100dvh + ${totalExtra}px)`;
        section.classList.add('gs-expect-scroll--ready');

        // Measure actual card height after layout switches (cards are now position:absolute).
        const cardH   = cards[0].offsetHeight;
        const CARD_GAP = 46; // px — matches the 2.875rem gap in CSS

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
    @endpush

    </x-page-hero>

</x-layouts::site>
