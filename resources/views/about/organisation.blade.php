{{--
    Over ons / Hoe we werken — /about/organisation (P-17)
    Restructured 2026-07 (spec: 2026-07-03-about-section-content-design.md,
    variant A): intro carries the three-tier story (organigram cut), the
    ho-deal columns became two shared titled-list-blocks, safety lives in the
    duo's text, the callout folded into the intro. Copy: lang/nl/about.php
    (organisation_*). Structure only; zero page-specific components.
--}}
<x-layouts::site :title="__('nav.organisation')" :description="__('meta.organisation')">

    <x-page-hero
        :eyebrow="__('nav.organisation')"
        :title="__('about.organisation_title')"
        illustration="img/illustrations/heart-30-sign.svg">

    {{-- HOE WE GEORGANISEERD ZIJN — the intro tells the whole three-tier story --}}
    <x-intro-text>
        <p>{{ __('about.organisation_intro_1') }}</p>
        <p>{{ __('about.organisation_intro_2') }}</p>
        <p>{{ __('about.organisation_intro_3') }}</p>
        <p>{{ __('about.organisation_intro_4') }}</p>
    </x-intro-text>

    {{-- WIE WAT DOET — two shared titled-list-blocks (Steun-ons component) --}}
    <section class="about-section about-section--wide">
        <x-section-heading>{{ __('about.organisation_who_title') }}</x-section-heading>
        <div class="grid gap-8 md:grid-cols-2">
            <x-titled-list-block :title="__('about.organisation_national_title')" level="h3">
                @foreach (__('about.organisation_national') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </x-titled-list-block>
            <x-titled-list-block :title="__('about.organisation_local_title')" level="h3">
                @foreach (__('about.organisation_local') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </x-titled-list-block>
        </div>
    </section>

    {{-- HET COÖRDINATIEDUO — carries safety & vorming (they run it) --}}
    <section class="about-section">
        <x-section-heading>{{ __('about.organisation_duo_title') }}</x-section-heading>
        <p>{{ __('about.organisation_duo_body') }}</p>
        <p class="about-section__link"><a href="{{ route('getting-started') }}">{{ __('about.organisation_duo_link') }}</a></p>
        {{-- Foto's + persoonlijke bio's nog aan te leveren door het duo. --}}
        <ul class="about-duo" role="list">
            <li><x-person-card name="Leticia" role="Coördinatie" /></li>
            <li><x-person-card name="Cecilia" role="Coördinatie" /></li>
        </ul>
    </section>

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta :heading="__('about.organisation_closing_heading')"
            :href="route('getting-started')" :label="__('about.organisation_closing_label')" />
    </x-slot:closing>

</x-layouts::site>
