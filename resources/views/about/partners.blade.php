{{--
    Over ons / Partners & sponsors — /about/partners (P-20)
    Built 2026-06-03 to the DESIGN.md kit. A credibility/social-proof leaf — register a
    notch more serious (ToV). Curated static copy (the `partners` table is faker rows
    with no logos/category — D-11). Real names, no lorem.
    Arrange/polish 2026-06-03 (Frederik): the body is ONE connected column of white
    sections separated by hairline dividers (not floating islands, not many bands). The
    single light-blue accent band is the enquiry/CTA at the end — the most important
    section — not the secondary "operationele partners". Imagery: crowd photo + logo wall.
    Copy: lang/{nl,fr}/partners.php (page.*). Plan:
    docs/wiki/design/30-skeleton/about.md + about-content.md + about-journey.md + partners.md
    FR additions (per "Partners: French content, prices and organisation types"): the
    formula prices move onto the page (carrying "To be confirmed") and the "Ce que nous
    offrons à nos partenaires" list appears — both French-only, marked, hidden on /nl.
--}}
@php
    $isFr = app()->getLocale() === 'fr';
@endphp
<x-layouts::site :title="__('nav.partners')" :description="__('meta.partners')">
<div class="partners-page">

    <x-page-hero
        :eyebrow="__('partners.page.hero.eyebrow')"
        :title="__('partners.page.hero.title')"
        size="compact">

    {{-- WIE ONS STEUNT — named institutional anchors (depth) + the full logo wall (breadth)
         in ONE section. The in-kind/bike-provision partners (Loopz, Kidical Mouse, My Kids
         Bikes) live in the wall + a one-line note — no dedicated cards: that is a
         family/resource story, not sponsor credibility. (arrange 2026-06-03, Frederik) --}}
    <section class="about-section about-section--wide">
        <x-section-heading>{{ __('partners.page.allies.heading') }}</x-section-heading>
        <p class="about-partners__intro">{{ __('partners.page.allies.intro') }}</p>
        <ul class="about-partner-grid" role="list">
            @foreach ($partners as $partner)
                <x-partner-card :name="$partner->name" data-partner-category="{{ $partner->category->value }}">{{ $partner->description }}</x-partner-card>
            @endforeach
        </ul>
        <figure class="partner-logo-wall">
            <img src="{{ asset('img/partners/partner-logos-2024.png') }}" alt="{{ __('partners.page.allies.logo_alt') }}" loading="lazy">
            <figcaption>{{ __('partners.page.allies.logo_caption') }}</figcaption>
        </figure>
        <p class="about-partners__note">{{ __('partners.page.allies.note') }}</p>
    </section>

    {{-- WAAROM PARTNER WORDEN — benefit hook (from the Sponsorformules "waarom steunen") --}}
    <section class="about-section about-section--wide">
        <x-section-heading>{{ __('partners.page.why.heading') }}</x-section-heading>
        <p class="about-partners__intro">{{ __('partners.page.why.intro') }}</p>
        <x-check-list>
            @foreach (__('partners.page.why.items') as $item)
                <li>{{ $item }}</li>
            @endforeach
        </x-check-list>
    </section>

    {{-- ONZE FORMULES — the two tracks. Prices live in the downloadable PDF on /nl;
         on /fr they move onto the page (from the copy doc) and carry "To be confirmed". --}}
    <section class="about-section about-section--wide">
        <x-section-heading>{{ __('partners.page.formules.heading') }}</x-section-heading>
        <p class="about-partners__intro">{{ __('partners.page.formules.intro') }}</p>
        <div class="partner-formules">
            <div class="partner-formule-track partner-formule-track--vzw">
                <h3>{{ __('partners.page.formules.vzw.title') }}</h3>
                <ul>
                    @foreach (__('partners.page.formules.vzw.items') as $item)
                        <li><strong>{{ $item['name'] }}</strong>@if (isset($item['price']))<span class="partner-formule__price"> — {{ $item['price'] }}</span>@endif: {{ $item['body'] }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="partner-formule-track partner-formule-track--bedrijf">
                <h3>{{ __('partners.page.formules.bedrijf.title') }}</h3>
                <ul>
                    @foreach (__('partners.page.formules.bedrijf.items') as $item)
                        <li><strong>{{ $item['name'] }}</strong>@if (isset($item['price']))<span class="partner-formule__price"> — {{ $item['price'] }}</span>@endif: {{ $item['body'] }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <x-to-be-confirmed :when="$isFr">
            <p class="about-partners__note">{{ __('partners.page.formules.vat_note') }}</p>
        </x-to-be-confirmed>
        <p class="about-partners__note">
            <a href="{{ asset('downloads/kidical-mass-sponsorformules.pdf') }}" target="_blank" rel="noopener noreferrer" class="more-link">{{ __('partners.page.formules.pdf') }}</a>
        </p>
    </section>

    {{-- WAT WE VRAGEN / UNE COLLABORATION QUI A DU SENS — charter essence + download.
         The French adds the "Ce que nous offrons à nos partenaires" list (FR-only). --}}
    <section class="about-section about-section--wide">
        <x-section-heading>{{ __('partners.page.collab.heading') }}</x-section-heading>
        <p class="about-partners__intro">{{ __('partners.page.collab.intro') }}</p>
        <p class="about-partners__intro">{{ __('partners.page.collab.intro_2') }}</p>
        <x-layout-proposal :when="$isFr">
            <div class="about-partners__offer">
                <h3>{{ __('partners.page.collab.offer.heading') }}</h3>
                <p>{{ __('partners.page.collab.offer.lead') }}</p>
                <x-check-list>
                    @foreach (__('partners.page.collab.offer.items') as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </x-check-list>
            </div>
        </x-layout-proposal>
        <p class="about-partners__note">
            <a href="{{ asset('downloads/kidical-mass-partnercharter.pdf') }}" target="_blank" rel="noopener noreferrer" class="more-link">{{ __('partners.page.collab.charter') }}</a>
        </p>
    </section>

    {{-- INTERESSE? — the one accent band: this is the primary action. Routed form
         (PAT-6) + email/phone fallback. Replaces the old mailto "black hole". --}}
    <section class="about-band about-band--light-blue">
        <div class="container mx-auto px-4 partner-enquiry">
            <div class="partner-enquiry__intro">
                <x-section-heading class="mb-4">{{ __('partners.page.enquiry.heading') }}</x-section-heading>
                <p>{{ __('partners.page.enquiry.intro') }}</p>
                <p class="partner-enquiry__fallback"><span class="partner-enquiry__fallback-lead">{{ __('partners.page.enquiry.fallback_lead') }}</span><br>
                    <a href="mailto:{{ config('kidicalmass.contact.email') }}" class="more-link">{{ config('kidicalmass.contact.email') }}</a><br>
                    <a href="tel:{{ config('kidicalmass.contact.phone_e164') }}" class="more-link">{{ config('kidicalmass.contact.phone') }}</a>
                </p>
            </div>
            <div class="partner-enquiry__form">
                <livewire:partner-enquiry />
            </div>
        </div>
    </section>

    </x-page-hero>

</div>
    @push('scripts')
    <x-scroll-reveal selector=".partner-card" :transform="true" />
    @endpush

</x-layouts::site>