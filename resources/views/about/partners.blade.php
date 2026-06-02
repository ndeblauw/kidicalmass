{{--
    Over ons / Partners & sponsors — /about/partners (P-20)
    Built 2026-06-03 to the DESIGN.md kit. A credibility/social-proof leaf — register a
    notch more serious (ToV), calmer palette. Built from CURATED STATIC copy on purpose:
    the `partners` table holds only faker rows with no logos and no category column, so it
    can't drive a real page yet (see D-11 / about-journey.md). Real names, no lorem.
    Partner descriptors are factual placeholders pending each partner's approval.
    Colour story: blue → white → light-blue → yellow. Structure only.
    Plan: docs/wiki/design/30-skeleton/about.md + about-content.md + about-journey.md
--}}
<x-layouts::site title="Partners & sponsors">

    {{-- HERO --}}
    <section class="activity-hero about-hero">
        <img src="{{ asset('img/logo-icon.png') }}" alt="" aria-hidden="true" class="activity-hero__daisy">
        <div class="container mx-auto px-4 activity-hero__inner">
            <div class="activity-hero__copy">
                <span class="about-hero__badge">{{ __('nav.about') }}</span>
                <h1>Partners &amp; sponsors</h1>
                <p class="about-hero__lead">Kidical Mass werkt niet alleen. Deze organisaties delen onze overtuiging dat kinderen betere straten verdienen.</p>
            </div>
            <div class="activity-hero__visual">
                <div class="activity-hero__photo about-hero__circle">
                    <img src="{{ asset('img/illustrations/kid-on-bike.png') }}" alt="" aria-hidden="true">
                </div>
            </div>
        </div>
    </section>

    {{-- INSTITUTIONEEL + BONDGENOTEN --}}
    <section class="about-section about-section--wide">
        <h2 class="about-section__title">Institutionele partners en bondgenoten</h2>
        <p class="about-partners__intro">Deze organisaties steunen Kidical Mass op nationaal of regionaal niveau, via financiering, infrastructuur of een gedeeld pleidooi.</p>
        <ul class="about-partner-grid" role="list">
            <li class="about-partner-card">
                <strong>Brussel Mobiliteit</strong>
                <p>Gewestelijke mobiliteitsdienst van Brussel. Met de steun van Brussel Mobiliteit.</p>
            </li>
            <li class="about-partner-card">
                <strong>Brussel Stad</strong>
                <p>Stadsbestuur van Brussel, partner op lokaal niveau.</p>
            </li>
            <li class="about-partner-card">
                <strong>Gemeente Schaarbeek</strong>
                <p>Gemeentebestuur, partner van de lokale afdeling.</p>
            </li>
            <li class="about-partner-card">
                <strong>Clean Cities Campaign</strong>
                <p>Europese campagne voor propere, kindvriendelijke mobiliteit. Samen achter #StreetsForKids.</p>
            </li>
        </ul>
    </section>

    {{-- OPERATIONEEL & IN-KIND --}}
    <section class="about-band about-band--light-blue">
        <div class="container mx-auto px-4">
            <h2 class="about-band__title">Operationele en in-kind partners</h2>
            <p class="about-partners__intro">Deze organisaties steunen Kidical Mass op het terrein: ze lenen materiaal uit, bieden logistieke steun of helpen gezinnen aan een fiets voor hun eerste rit.</p>
            <ul class="about-partner-grid" role="list">
                <li class="about-partner-card about-partner-card--plain">
                    <strong>Loopz</strong>
                    <p>Fietsverhuur voor kinderen en volwassenen, met een korting voor wie meefietst.</p>
                </li>
                <li class="about-partner-card about-partner-card--plain">
                    <strong>Kidical Mouse</strong>
                    <p>Zet bakfietsen klaar aan de start van sommige Brusselse ritten.</p>
                </li>
                <li class="about-partner-card about-partner-card--plain">
                    <strong>My Kids Bikes</strong>
                    <p>Abonnement op een kwaliteitsvolle kinderfiets die meegroeit met je kind.</p>
                </li>
            </ul>
            <p class="about-partners__note">Geen fiets? <a href="{{ route('find-a-bike') }}">Bekijk alle opties om er een te lenen of te huren →</a></p>
        </div>
    </section>

    {{-- CTA — partner worden (mailto) --}}
    <x-about-cta
        title="{{ __('partners.become_partner') }}"
        sub="Of je nu een lokaal bedrijf, een gemeente of een nationale organisatie bent: als je gelooft in kindvriendelijke straten, praten we graag.">
        <x-slot:actions>
            <a href="mailto:bike@kidicalmass.be" class="about-cta__btn about-cta__btn--primary link-plain">Neem contact op →</a>
        </x-slot:actions>
    </x-about-cta>

    @push('scripts')
    <x-about-reveal selector=".about-partner-card" :transform="true" />
    @endpush

</x-layouts::site>
