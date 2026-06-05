{{--
    Over ons / Pers — /about/press (P-19)
    Built 2026-06-03 to the DESIGN.md kit. A credibility leaf, built contact-forward and
    HONEST: there is no Press model/table yet and the legacy outlet URLs are unverified, so
    we ship the press contact + an honest empty state rather than fabricating coverage
    (see D-11 / about-journey.md). Register a notch more serious (ToV).
    Colour story: blue → white → light-blue → white. Structure only.
    Plan: docs/wiki/design/30-skeleton/about.md + about-content.md + about-journey.md
--}}
<x-layouts::site title="Pers">

    <x-page-hero
        eyebrow="Pers"
        title="Het verhaal van de beweging.">

    {{-- INTRO + CONTACT --}}
    <section class="about-section about-section--wide">
        <div class="about-press">
            <div class="about-press__intro">
                <h2 class="about-section__title">Journalisten, we praten graag</h2>
                <p>We brengen je in contact met lokale trekkers, delen cijfers, regelen een fotomoment bij een volgende fietsparade of geven achtergrond bij de beweging.</p>
                <ul class="about-press__offer" role="list">
                    <li>Contact met lokale afdelingen en gezinnen</li>
                    <li>Cijfers en achtergrond over de beweging</li>
                    <li>Een fotomoment bij een aankomende rit</li>
                </ul>
            </div>
            <aside class="about-contact-card">
                <span class="about-contact-card__label">Perscontact</span>
                <a href="mailto:bike@kidicalmass.be" class="about-contact-card__email">bike@kidicalmass.be</a>
                <p class="about-contact-card__note">We antwoorden zo snel als vrijwilligers dat kunnen.</p>
            </aside>
        </div>

        {{-- Eerder in de media — alleen waarheidsgetrouwe titelnamen, geen verzonnen
             links of koppen. Het gelinkte archief volgt met een Press-model (zie D-11). --}}
        <div class="about-press__outlets">
            <span class="about-press__outlets-label">Eerder verschenen in</span>
            <ul role="list">
                <li>RTBF</li>
                <li>BX1</li>
                <li>BRUZZ</li>
                <li>La DH</li>
                <li>HLN</li>
                <li>Het Nieuwsblad</li>
            </ul>
        </div>
    </section>

    {{-- PERSOVERZICHT — honest empty state (no Press model yet, see D-11) --}}
    <section class="about-band about-band--light-blue">
        <div class="container mx-auto px-4">
            <div class="about-empty">
                <h2 class="about-empty__title">We bouwen aan een persoverzicht</h2>
                <p>Kidical Mass kwam de afgelopen jaren in heel wat kranten, radio en tv. We brengen die berichtgeving binnenkort samen op één plek. Schreef je over Kidical Mass en wil je dat je artikel hier verschijnt? Laat het ons weten via <a href="mailto:bike@kidicalmass.be">bike@kidicalmass.be</a>.</p>
            </div>
        </div>
    </section>

    {{-- ACHTERGROND --}}
    <section class="about-section">
        <p class="about-section__link"><a href="{{ route('about.mission') }}">Achtergrond en cijfers: lees onze missie →</a></p>
    </section>

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta heading="Vragen van de pers?"
            :href="route('contact')" label="Neem contact op" />
    </x-slot:closing>

</x-layouts::site>
