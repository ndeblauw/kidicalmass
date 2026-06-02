{{--
    Over ons — /about (P-14)
    Built 2026-06-03 to the DESIGN.md kit. A navigational hub for "deciders & deepeners":
    orient ("what's in this section?") and route ("where should I go?"). Reuses
    .activity-hero* (blue) + a 6-card nav grid (red Flux-icon chips) + a mini stat bar +
    the shared closing CTA. Colour story: blue → white → light-blue → yellow.
    Plan: docs/wiki/design/30-skeleton/about.md + about-journey.md
--}}
<x-layouts::site title="Over ons">

    {{-- HERO --}}
    <section class="activity-hero about-hero">
        <img src="{{ asset('img/logo-icon.png') }}" alt="" aria-hidden="true" class="activity-hero__daisy">
        <div class="container mx-auto px-4 activity-hero__inner">
            <div class="activity-hero__copy">
                <h1>Over ons</h1>
                <p class="about-hero__lead">Kidical Mass organiseert fietsparades voor gezinnen in heel België en pleit voor kindvriendelijke straten. Een vrijwilligersnetwerk, lokaal geworteld en samen gecoördineerd.</p>
            </div>
            <div class="activity-hero__visual">
                <div class="activity-hero__photo about-hero__circle">
                    <img src="{{ asset('img/illustrations/kid-on-bike.png') }}" alt="" aria-hidden="true">
                </div>
            </div>
        </div>
    </section>

    {{-- SUBPAGINA'S — 6 nav cards --}}
    <section class="about-section about-section--wide">
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
                <li class="about-stat"><span class="about-stat__num">16+</span><span class="about-stat__label">gemeenten in heel België</span></li>
                <li class="about-stat"><span class="about-stat__num">150+</span><span class="about-stat__label">fietsparades sinds 2020</span></li>
                <li class="about-stat"><span class="about-stat__num">120</span><span class="about-stat__label">actieve vrijwilligers</span></li>
            </ul>
        </div>
    </section>

    {{-- CTA --}}
    <x-about-cta
        title="Zin om mee te doen?"
        sub="Rij mee met een rit bij jou in de buurt, of geef zelf mee vorm aan de beweging." />

    @push('scripts')
    <x-about-reveal selector=".about-nav-card" :transform="true" />
    @endpush

</x-layouts::site>
