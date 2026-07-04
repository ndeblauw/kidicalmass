{{--
    Over ons / Pers — /about/press (P-19)
    Restructured 2026-07 (spec: 2026-07-03-about-section-content-design.md,
    variant B): one contact section + the year-grouped PressArticle archive.
    Outlet strip and closing CTA cut (the archive shows the outlets; the page
    IS the contact). Copy: lang/nl/about.php (press_*). Structure only.
--}}
<x-layouts::site :title="__('nav.press')" :description="__('meta.press')">

    <x-page-hero
        :eyebrow="__('nav.press')"
        :title="__('about.press_title')">

    {{-- CONTACT — one section: pitch, background link, perscontact card --}}
    <section class="about-section about-section--wide">
        <div class="press-contact">
            <div class="press-contact__intro">
                <x-section-heading>{{ __('about.press_contact_title') }}</x-section-heading>
                <p>{{ __('about.press_contact_body') }}</p>
                <p><a href="{{ route('about.mission') }}" class="more-link">{{ __('about.press_background_link') }}</a></p>
            </div>
            <x-info-card :label="__('about.press_contact_label')">
                <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                <p class="info-card__note">{{ __('about.press_contact_note') }}</p>
            </x-info-card>
        </div>
    </section>

    {{-- PERSOVERZICHT — year-grouped archive (PressArticle, admin-maintained) --}}
    <section class="about-band about-band--light-blue">
        <div class="container mx-auto px-4">
            @if ($articlesByYear->isNotEmpty())
                <x-section-heading class="mb-8">{{ __('about.press_overview_title') }}</x-section-heading>
                <x-press-archive :articles-by-year="$articlesByYear" />
            @else
                <x-empty-state :heading="__('about.press_empty_title')">
                    {{ __('about.press_empty_body') }}
                </x-empty-state>
            @endif
        </div>
    </section>

    </x-page-hero>

</x-layouts::site>
