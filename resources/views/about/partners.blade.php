{{--
    Over ons / Partners & sponsors — /about/partners (P-20)
    Built 2026-06-03 to the DESIGN.md kit. A credibility/social-proof leaf — register a
    notch more serious (ToV). Curated static copy (the `partners` table is faker rows
    with no logos/category — D-11). Real names, no lorem.
    Arrange/polish 2026-06-03 (Frederik): the body is ONE connected column of white
    sections separated by hairline dividers (not floating islands, not many bands). The
    single light-blue accent band is the enquiry/CTA at the end — the most important
    section — not the secondary "operationele partners". Imagery: crowd photo + logo wall.
    Plan: docs/wiki/design/30-skeleton/about.md + about-content.md + about-journey.md + partners.md
--}}
<x-layouts::site title="Partners & sponsors" :description="__('meta.partners')">
<div class="partners-page">

    <x-page-hero
        eyebrow="Partners & sponsors"
        title="Samen sterker voor veilige straten.">

    {{-- WIE ONS STEUNT — named institutional anchors (depth) + the full logo wall (breadth)
         in ONE section. The in-kind/bike-provision partners (Loopz, Kidical Mouse, My Kids
         Bikes) live in the wall + a one-line find-a-bike pointer — no dedicated cards: that
         is a family/resource story, not sponsor credibility. (arrange 2026-06-03, Frederik) --}}
    <section class="about-section about-section--wide">
        <x-section-heading>Onze partners en bondgenoten</x-section-heading>
        <p class="about-partners__intro">Deze organisaties steunen Kidical Mass op nationaal of regionaal niveau, via financiering, infrastructuur of een gedeeld pleidooi.</p>
        <ul class="about-partner-grid" role="list">
            @foreach ($partners as $partner)
                <x-partner-card :name="$partner->name" data-partner-category="{{ $partner->category->value }}">{{ $partner->description_nl }}</x-partner-card>
            @endforeach
        </ul>
        <figure class="partner-logo-wall">
            <img src="{{ asset('img/partners/partner-logos-2024.png') }}" alt="Logo's van de vele partners en bondgenoten van Kidical Mass, waaronder Brussel Mobiliteit, Pro Velo, Cyclo, GRACQ, Fietsersbond, Bruzz en vele anderen" loading="lazy">
            <figcaption>En vele anderen die Kidical Mass mee mogelijk maken.</figcaption>
        </figure>
        <p class="about-partners__note">Op het terrein helpen partners zoals Loopz en My Kids Bikes gezinnen aan een fiets. Geen fiets? <a href="{{ route('find-a-bike') }}" class="more-link">Bekijk de opties →</a></p>
    </section>

    {{-- WAAROM PARTNER WORDEN — benefit hook (from the Sponsorformules "waarom steunen") --}}
    <section class="about-section about-section--wide">
        <x-section-heading>Waarom partner of sponsor worden?</x-section-heading>
        <p class="about-partners__intro">Als partner steun je een beweging die elke maand honderden gezinnen veilig op de fiets krijgt. En je bouwt mee aan een stad op maat van kinderen.</p>
        <x-check-list>
            <li>Je draagt bij aan kindvriendelijke, veilige straten in elke buurt.</li>
            <li>Je ondersteunt burgerparticipatie en duurzame mobiliteit.</li>
            <li>Je krijgt positieve zichtbaarheid bij gezinnen, buurtbewoners en beleidsmakers.</li>
        </x-check-list>
    </section>

    {{-- ONZE FORMULES — on-page summary of the two tracks; prices live in the
         downloadable PDF (provisional, pending Leticia's national-scope OK). --}}
    <section class="about-section about-section--wide">
        <x-section-heading>Onze formules</x-section-heading>
        <p class="about-partners__intro">We werken met formules op maat, in twee sporen. Je kiest zelf hoe zichtbaar je wil zijn: van een vermelding op sociale media tot je logo op onze website, flyers en banners.</p>
        <div class="partner-formules">
            <div class="partner-formule-track">
                <h3>Voor vzw's en verenigingen</h3>
                <ul>
                    <li><strong>Supporter</strong>: vermelding op sociale media</li>
                    <li><strong>Partner</strong>: logo op de website + sociale media</li>
                    <li><strong>Community Partner</strong>: logo op de website, sociale media en flyers van een event</li>
                </ul>
            </div>
            <div class="partner-formule-track">
                <h3>Voor bedrijven</h3>
                <ul>
                    <li><strong>Friend</strong>: logo op de website + sociale media</li>
                    <li><strong>Sponsor</strong>: logo op de website, sociale media en alle event-flyers</li>
                    <li><strong>Main Partner</strong>: logo overal, plus ruimte en aanwezigheid op events</li>
                </ul>
            </div>
        </div>
        <p class="about-partners__note">
            <a href="{{ asset('downloads/kidical-mass-sponsorformules.pdf') }}" target="_blank" rel="noopener noreferrer" class="more-link">Bekijk alle formules en tarieven (pdf) →</a>
        </p>
    </section>

    {{-- WAT WE VRAGEN — charter essence + download (the charter doubles as a values filter) --}}
    <section class="about-section about-section--wide">
        <x-section-heading>Wat we van partners vragen</x-section-heading>
        <p class="about-partners__intro">Kidical Mass is een burgerbeweging, geen reclamebord. We werken samen met partners die onze waarden delen: kindvriendelijkheid, veiligheid, duurzaamheid en inclusie. Je steun komt zonder voorwaarden die onze werking of onze boodschap sturen, en we blijven onafhankelijk en niet-commercieel.</p>
        <p class="about-partners__note">
            <a href="{{ asset('downloads/kidical-mass-partnercharter.pdf') }}" target="_blank" rel="noopener noreferrer" class="more-link">Lees ons volledige sponsor- en partnercharter (pdf) →</a>
        </p>
    </section>

    {{-- INTERESSE? — the one accent band: this is the primary action. Routed form
         (PAT-6) + email/phone fallback. Replaces the old mailto "black hole". --}}
    <section class="about-band about-band--light-blue">
        <div class="container mx-auto px-4 partner-enquiry">
            <div class="partner-enquiry__intro">
                <x-section-heading class="mb-4">Interesse? Laten we praten.</x-section-heading>
                <p>Vul kort in wie je bent en waar je interesse naar uitgaat. We nemen snel contact op om samen de juiste formule te vinden. Je verbindt je tot niets.</p>
                <p class="partner-enquiry__fallback">Liever rechtstreeks?<br>
                    <a href="mailto:bike@kidicalmass.be" class="more-link">bike@kidicalmass.be</a><br>
                    <a href="tel:+32495812795" class="more-link">0495 81 27 95</a>
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
