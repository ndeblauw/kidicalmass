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

    <x-page-hero
        eyebrow="Voor het eerst"
        title="Kom zoals je bent."
        illustration="img/illustrations/kid-on-scooter.png">

    {{-- WAT JE MAG VERWACHTEN — reuses the ride page's promises band --}}
    <section class="activity-promises gs-expect">
        <div class="activity-promises__layout">

            <div class="activity-promises__illustration">
                <h2>Wat je mag verwachten op een rit</h2>
                <img src="{{ asset('img/illustrations/kid-on-scooter.png') }}" alt="" aria-hidden="true" loading="lazy">
            </div>

            <ul class="activity-promises__col" role="list">
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.clock variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>Kort en rustig</strong>
                    <p>5 à 7 km op het tempo van het jongste kind, zelden meer dan een uur.</p>
                </li>
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.musical-note variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>Muziek onderweg</strong>
                    <p>Er is altijd een geluidssysteem. Een vrolijke, luidruchtige fietsparade door de buurt.</p>
                </li>
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.map-pin variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>Vaste startplaats</strong>
                    <p>Elke rit vertrekt op een vaste plek, vermeld op de eventpagina. Gewoon daar opdagen.</p>
                </li>
            </ul>

            <ul class="activity-promises__col" role="list">
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.ticket variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>Gratis, geen inschrijving</strong>
                    <p>Geen ticket, geen registratie, geen kosten. Kom gewoon naar de start.</p>
                </li>
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.users variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>Alle leeftijden welkom</strong>
                    <p>Vanaf een jaar of 3, op eigen fiets, in een bakfiets of op een kinderzitje.</p>
                </li>
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.shield-check variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>Minstens vier roze hesjes</strong>
                    <p>Opgeleide begeleiders rijden vooraan en achteraan en houden elke kruising vrij, zodat geen kind achterblijft.</p>
                </li>
            </ul>

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

    {{-- Scroll reveal for the expectation cards (mirrors the ride page) --}}
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        const cards = document.querySelectorAll('.gs-expect .activity-promises__item');
        cards.forEach((card, i) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.4s cubic-bezier(0.25, 1, 0.5, 1), transform 0.4s cubic-bezier(0.25, 1, 0.5, 1)';
            card.style.transitionDelay = `${i * 80}ms`;
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        cards.forEach(card => observer.observe(card));
    });
    </script>
    @endpush

    </x-page-hero>

</x-layouts::site>
