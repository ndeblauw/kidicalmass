{{--
    Over ons — /about (P-14)
    Built 2026-06-03 to the DESIGN.md kit. A navigational hub for "deciders & deepeners":
    orient ("what's in this section?") and route ("where should I go?"). Intent strip
    routes the act-exits (including Pers + Partners); a 2x2 nav grid covers the read
    path. The stats deck lives on Wat we doen (mission) — the hub carries none.
    Plan: docs/wiki/design/30-skeleton/about.md + about-journey.md
--}}
<x-layouts::site title="Over ons" :description="__('meta.about')">

    <x-page-hero
        eyebrow="Over ons"
        title="Samen maken we straten voor kinderen."
        illustration="img/illustrations/cyclist-peace-sign.svg">

    {{-- Lead, relocated onto the panel (the hub has no separate intro section). --}}
    <x-intro-text>
        <p>Kidical Mass organiseert fietsparades voor gezinnen in heel België en pleit voor kindvriendelijke straten. Een vrijwilligersnetwerk, lokaal geworteld en samen gecoördineerd.</p>
    </x-intro-text>

    {{-- WAAR BEN JE NAAR OP ZOEK — intention triage. Routes to the EXITS deciders
         actually came for (act), above the browse menu (read). --}}
    <section class="about-section about-section--wide">
        <x-section-heading>Waar ben je naar op zoek?</x-section-heading>
        <ul class="about-intent" role="list">
            <li><x-intent-card :href="route('volunteer')" label="Een groep starten of meehelpen" /></li>
            <li><x-intent-card :href="route('about.press')" label="Ik ben pers" /></li>
            <li><x-intent-card :href="route('about.partners')" label="Partner of sponsor worden" /></li>
            <li><x-intent-card :href="route('membership')" label="De beweging steunen" /></li>
        </ul>
    </section>

    {{-- SUBPAGINA'S — the browse path (4 nav cards; Pers + Partners route via the
         intent strip above). --}}
    <section class="about-section about-section--wide">
        <x-section-heading>Of lees meer over de beweging</x-section-heading>
        <ul class="about-nav" role="list">
            <li>
                <x-nav-card :href="route('about.mission')" icon="flag" :title="__('nav.mission')">
                    Fietsparades, lokale groepen en de weg naar veilige straten.
                </x-nav-card>
            </li>
            <li>
                <x-nav-card :href="route('about.vision')" icon="eye" :title="__('nav.vision')">
                    Vier duidelijke vragen aan steden en gemeenten.
                </x-nav-card>
            </li>
            <li>
                <x-nav-card :href="route('about.organisation')" icon="building-office-2" :title="__('nav.organisation')">
                    Lokaal geworteld, licht gecoördineerd, gedragen door vrijwilligers.
                </x-nav-card>
            </li>
            <li>
                <x-nav-card :href="route('articles.index')" icon="newspaper" :title="__('nav.news')">
                    Updates uit het netwerk.
                </x-nav-card>
            </li>
        </ul>
    </section>

    @push('scripts')
    <x-scroll-reveal selector=".intent-card, .nav-card" :transform="true" />
    @endpush

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta heading="Rij mee met de buurt"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>

</x-layouts::site>
