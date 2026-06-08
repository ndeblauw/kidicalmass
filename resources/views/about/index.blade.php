{{--
    Over ons — /about (P-14)
    Built 2026-06-03 to the DESIGN.md kit. A navigational hub for "deciders & deepeners":
    orient ("what's in this section?") and route ("where should I go?"). Reuses
    .activity-hero* (blue) + a 6-card nav grid (red Flux-icon chips) + a mini stat bar +
    the shared closing CTA. Colour story: blue → white → light-blue → yellow.
    Plan: docs/wiki/design/30-skeleton/about.md + about-journey.md
--}}
<x-layouts::site title="Over ons">

    <x-page-hero
        eyebrow="Over ons"
        title="Samen maken we straten voor kinderen."
        illustration="img/illustrations/tree-round.png">

    {{-- Lead, relocated onto the panel (the hub has no separate intro section). --}}
    <x-intro-text>
        <p>Kidical Mass organiseert fietsparades voor gezinnen in heel België en pleit voor kindvriendelijke straten. Een vrijwilligersnetwerk, lokaal geworteld en samen gecoördineerd.</p>
    </x-intro-text>

    {{-- WAAR BEN JE NAAR OP ZOEK — intention triage. Routes to the EXITS deciders
         actually came for (act), above the browse menu (read). --}}
    <section class="about-section about-section--wide">
        <x-section-heading>Waar ben je naar op zoek?</x-section-heading>
        <ul class="about-intent" role="list">
            <li>
                <a href="{{ route('volunteer') }}" class="about-intent-card link-plain">
                    <span class="about-intent-card__label">Een groep starten of meehelpen</span>
                    <span class="about-intent-card__arrow" aria-hidden="true">→</span>
                </a>
            </li>
            <li>
                <a href="{{ route('about.press') }}" class="about-intent-card link-plain">
                    <span class="about-intent-card__label">Ik ben pers</span>
                    <span class="about-intent-card__arrow" aria-hidden="true">→</span>
                </a>
            </li>
            <li>
                <a href="{{ route('about.partners') }}" class="about-intent-card link-plain">
                    <span class="about-intent-card__label">Partner of sponsor worden</span>
                    <span class="about-intent-card__arrow" aria-hidden="true">→</span>
                </a>
            </li>
            <li>
                <a href="{{ route('membership') }}" class="about-intent-card link-plain">
                    <span class="about-intent-card__label">De beweging steunen</span>
                    <span class="about-intent-card__arrow" aria-hidden="true">→</span>
                </a>
            </li>
        </ul>
    </section>

    {{-- SUBPAGINA'S — the browse path (6 nav cards) --}}
    <section class="about-section about-section--wide">
        <x-section-heading>Of lees meer over de beweging</x-section-heading>
        <ul class="about-nav" role="list">
            <li>
                <a href="{{ route('about.mission') }}" class="about-nav-card link-plain">
                    <span class="about-nav-card__chip"><flux:icon.flag variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                    <h2 class="about-nav-card__title">Missie</h2>
                    <p class="about-nav-card__desc">Wat we doen en waarom.</p>
                    <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                </a>
            </li>
            <li>
                <a href="{{ route('about.vision') }}" class="about-nav-card link-plain">
                    <span class="about-nav-card__chip"><flux:icon.eye variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                    <h2 class="about-nav-card__title">Visie</h2>
                    <p class="about-nav-card__desc">Waarvoor we staan.</p>
                    <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                </a>
            </li>
            <li>
                <a href="{{ route('about.organisation') }}" class="about-nav-card link-plain">
                    <span class="about-nav-card__chip"><flux:icon.building-office-2 variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                    <h2 class="about-nav-card__title">Organisatie</h2>
                    <p class="about-nav-card__desc">Hoe we werken.</p>
                    <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                </a>
            </li>
            <li>
                <a href="{{ route('articles.index') }}" class="about-nav-card link-plain">
                    <span class="about-nav-card__chip"><flux:icon.newspaper variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                    <h2 class="about-nav-card__title">Nieuws</h2>
                    <p class="about-nav-card__desc">Updates uit het netwerk.</p>
                    <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                </a>
            </li>
            <li>
                <a href="{{ route('about.press') }}" class="about-nav-card link-plain">
                    <span class="about-nav-card__chip"><flux:icon.megaphone variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                    <h2 class="about-nav-card__title">Pers</h2>
                    <p class="about-nav-card__desc">Kidical Mass in de media.</p>
                    <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                </a>
            </li>
            <li>
                <a href="{{ route('about.partners') }}" class="about-nav-card link-plain">
                    <span class="about-nav-card__chip"><flux:icon.user-group variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                    <h2 class="about-nav-card__title">Partners</h2>
                    <p class="about-nav-card__desc">Wie de beweging mee mogelijk maakt.</p>
                    <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                </a>
            </li>
        </ul>
    </section>

    {{-- MINI STATBAR — full-bleed light-blue band --}}
    <section class="about-stats" aria-label="Kidical Mass in cijfers">
        <div class="container mx-auto px-4">
            <ul class="about-stats__grid about-stats__grid--three" role="list">
                {{-- TODO [concern]: Stats are hardcoded. Make dynamic: gemeenten = count(active groups), fietsparades = count(past activities), vrijwilligers = from a config/CMS field. See concerns register. --}}
                <li class="about-stat"><span class="about-stat__num">20</span><span class="about-stat__label">gemeenten in heel België</span></li>
                <li class="about-stat"><span class="about-stat__num">200+</span><span class="about-stat__label">fietsparades sinds 2020</span></li>
                <li class="about-stat"><span class="about-stat__num">300+</span><span class="about-stat__label">actieve vrijwilligers</span></li>
            </ul>
        </div>
    </section>

    @push('scripts')
    <x-about-reveal selector=".about-intent-card, .about-nav-card" :transform="true" />
    @endpush

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta heading="Rij mee met de buurt"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>

</x-layouts::site>
