{{--
    Over ons / Wat we vragen — /about/vision (P-16)
    Restructured 2026-07 (spec: 2026-07-03-about-section-content-design.md,
    variant B): tightened statement, four demands with the parent voices nested
    under the demand they speak to, the manifest as an info-card, closing CTA
    chained to Hoe we werken. Copy: lang/nl/about.php (vision_*). Structure only.

    Note: <x-numbered-item> renders its slot inside a <p>, and <x-pull-quote>
    renders a <figure> — nesting a figure inside a p is invalid HTML, so each
    parent-voice quote sits as a sibling directly after its numbered-item
    rather than inside its slot. Both are wrapped together in a single <li>
    since an <ol>'s only permitted children are <li> elements.
--}}
<x-layouts::site :title="__('nav.vision')" :description="__('meta.vision')">

    <x-page-hero
        :eyebrow="__('nav.vision')"
        :title="__('about.vision_title')"
        size="compact">

    {{-- POSITIESTATEMENT --}}
    <x-intro-text size="lead">
        <p>{{ __('about.vision_statement_1') }}</p>
        <p>{{ __('about.vision_statement_2') }}</p>
    </x-intro-text>

    {{-- VIER EISEN — parent voices nested under the demand they speak to --}}
    <section class="about-band about-band--light-blue">
        <div class="container mx-auto px-4">
            <x-section-heading class="mb-8">{{ __('about.vision_demands_title') }}</x-section-heading>
            <ol class="about-demand-grid">
                <li>
                    <x-numbered-item number="1" :title="__('about.vision_demand1_title')">
                        {{ __('about.vision_demand1_body') }}
                    </x-numbered-item>
                    <x-pull-quote variant="card" :attribution="$visionQuote1?->attribution ?? __('about.vision_quote_fatima_attribution')">
                        {{ $visionQuote1?->quote ?? __('about.vision_quote_fatima') }}
                    </x-pull-quote>
                </li>
                <li>
                    <x-numbered-item number="2" :title="__('about.vision_demand2_title')">
                        {{ __('about.vision_demand2_body') }}
                    </x-numbered-item>
                    <x-pull-quote variant="card" :attribution="$visionQuote2?->attribution ?? __('about.vision_quote_camille_attribution')">
                        {{ $visionQuote2?->quote ?? __('about.vision_quote_camille') }}
                    </x-pull-quote>
                </li>
                <li>
                    <x-numbered-item number="3" :title="__('about.vision_demand3_title')">
                        {{ __('about.vision_demand3_body') }}
                    </x-numbered-item>
                </li>
                <li>
                    <x-numbered-item number="4" :title="__('about.vision_demand4_title')">
                        {{ __('about.vision_demand4_body') }}
                    </x-numbered-item>
                </li>
            </ol>
        </div>
    </section>

    {{-- MANIFEST — same info-card component as the Pers contact card --}}
    <section class="about-section">
        <x-info-card :label="__('about.vision_manifest_label')">
            <p>{{ __('about.vision_manifest_body') }}</p>
            <a href="{{ asset('downloads/kidical-mass-manifest.pdf') }}" target="_blank" rel="noopener noreferrer" class="info-card__link">{{ __('about.vision_manifest_link') }}</a>
        </x-info-card>
    </section>

    @push('scripts')
    <x-scroll-reveal selector=".about-demand-grid > li" :transform="true" />
    @endpush

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta :heading="__('about.vision_closing_heading')"
            :href="route('about.organisation')" :label="__('about.vision_closing_label')" />
    </x-slot:closing>

</x-layouts::site>
