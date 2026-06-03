{{--
    Getting Started — "Voor het eerst mee"
    Surface pass 2026-06-02 (Frederik-guided): aligned to the ride/show layout system.
    - HERO reuses .activity-hero* — solid blue full-bleed, daisy, circular illustration,
      tilted white H1, sky badge.
    - "Wat je mag verwachten" reuses .activity-promises* verbatim — sky-blue band, big H2 +
      illustration, white tilted cards with red Flux-icon chips (a fuller sibling of the
      ride page's "Wat kun je verwachten?").
    - FAQ kept as the accordion (net-new; the ride page has none), contained on white.
    - CTA is a full-bleed yellow band.
    Structure only; appearance lives in app.css. Copy unchanged from the distilled version.
    Plan: docs/wiki/design/30-skeleton/getting-started.md
--}}
<x-layouts::site title="Voor het eerst mee">

    <x-page-hero eyebrow="Voor het eerst" title="Kom zoals je bent, je eerste rit wordt een feest." illustration="img/illustrations/kid-on-scooter.png">

    {{-- WAT JE MAG VERWACHTEN — scroll-stacking cards (desktop); static list (mobile) --}}
    <section class="gs-expect-scroll">
        <div class="gs-expect-pin">

            <div class="gs-expect-left">
                <h2>Wat je mag verwachten op een rit</h2>
                <img src="{{ asset('img/illustrations/kid-on-scooter.png') }}" alt="" aria-hidden="true" loading="lazy">
            </div>

            <div class="gs-expect-right">
                <div class="gs-expect-cards">

                    <div class="gs-expect-card" data-idx="0">
                        <div class="gs-expect-card__icon">
                            <flux:icon.clock variant="solid" aria-hidden="true" />
                        </div>
                        <strong>Kort en rustig</strong>
                        <p>5 à 7 km op het tempo van het jongste kind, zelden meer dan een uur.</p>
                    </div>

                    <div class="gs-expect-card" data-idx="1">
                        <div class="gs-expect-card__icon">
                            <flux:icon.musical-note variant="solid" aria-hidden="true" />
                        </div>
                        <strong>Muziek onderweg</strong>
                        <p>Er is altijd een geluidssysteem. Een vrolijke, luidruchtige fietsparade door de buurt.</p>
                    </div>

                    <div class="gs-expect-card" data-idx="2">
                        <div class="gs-expect-card__icon">
                            <flux:icon.map-pin variant="solid" aria-hidden="true" />
                        </div>
                        <strong>Vaste startplaats</strong>
                        <p>Elke rit vertrekt op een vaste plek, vermeld op de eventpagina. Gewoon daar opdagen.</p>
                    </div>

                    <div class="gs-expect-card" data-idx="3">
                        <div class="gs-expect-card__icon">
                            <flux:icon.ticket variant="solid" aria-hidden="true" />
                        </div>
                        <strong>Gratis, geen inschrijving</strong>
                        <p>Geen ticket, geen registratie, geen kosten. Kom gewoon naar de start.</p>
                    </div>

                    <div class="gs-expect-card" data-idx="4">
                        <div class="gs-expect-card__icon">
                            <flux:icon.users variant="solid" aria-hidden="true" />
                        </div>
                        <strong>Alle leeftijden welkom</strong>
                        <p>Vanaf een jaar of 3, op eigen fiets, in een bakfiets of op een kinderzitje.</p>
                    </div>

                    <div class="gs-expect-card" data-idx="5">
                        <div class="gs-expect-card__icon">
                            <flux:icon.shield-check variant="solid" aria-hidden="true" />
                        </div>
                        <strong>Minstens vier roze hesjes</strong>
                        <p>Opgeleide begeleiders rijden vooraan en achteraan en houden elke kruising vrij, zodat geen kind achterblijft.</p>
                    </div>

                </div>
            </div>

        </div>
    </section>

    {{-- VEELGESTELDE VRAGEN — accordion (contained) --}}
    <section class="gs-faq-section">
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
    </section>

    {{-- PRIMARY CTA — full-bleed yellow band --}}
    <section class="gs-cta">
        <div class="container mx-auto px-4 gs-cta__inner">
            <h2>Klaar voor je eerste rit?</h2>
            <p class="gs-cta__sub">Zoek een rit bij jou in de buurt en kom gewoon langs.</p>
            <a href="{{ route('activities.index') }}" class="gs-cta__btn link-plain">Vind een rit bij jou in de buurt →</a>
        </div>
    </section>

    {{-- Scroll-stacking animation for the expectations cards (desktop only) --}}
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        if (!window.matchMedia('(min-width: 1024px)').matches) return;

        const section = document.querySelector('.gs-expect-scroll');
        if (!section) return;

        const stack  = section.querySelector('.gs-expect-cards');
        const cards  = [...stack.querySelectorAll('.gs-expect-card')];
        const N      = cards.length;

        // Final resting position for each card in the stack.
        // Index 0 arrives first (sits at the bottom), index N-1 arrives last (sits on top).
        const FINALS = [
            { y: 56, r: -1.5 },
            { y: 44, r:  1.2 },
            { y: 32, r: -0.8 },
            { y: 20, r:  1.5 },
            { y: 10, r: -0.5 },
            { y:  0, r:  0.5 },
        ];

        const scrollPerCard = window.innerHeight * 0.45;
        const totalExtra    = N * scrollPerCard;

        // Extend section height so sticky pin has enough room to animate all cards.
        section.style.height = `calc(100dvh + ${totalExtra}px)`;

        // Activate the sticky two-column layout via CSS.
        section.classList.add('gs-expect-scroll--ready');

        // Size the stack container once the layout has settled.
        stack.style.height = '340px';

        // Z-order: last card is always visually on top.
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

                card.style.opacity   = String(Math.min(1, t * 4));
                card.style.transform = `translateY(${f.y + 200 * (1 - e)}px) rotate(${f.r * e}deg)`;
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
