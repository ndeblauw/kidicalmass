{{--
    Over ons / Wat we vragen — /about/vision (P-16)
    Arrange pass 2026-07-04 (design-choices-visie pick: variant B, verhaalkolom
    + stille rail): the light-blue band and the 2-col demand grid are gone. The
    four demands run as subtitled body text (h3 + p) in one story column with
    the parent voices woven between them (mission grammar), a closing line
    rounds the demands off, and the manifest sits as a sticky info-card in the
    right rail (press grammar). Copy: lang/nl/about.php (vision_*).
    Structure only.
--}}
<x-layouts::site :title="__('nav.vision')" :description="__('meta.vision')">

    <x-page-hero
        :eyebrow="__('nav.vision')"
        :title="__('about.vision_title')"
        size="compact">

    <section class="about-section about-section--wide">
        <div class="grid items-start gap-10 md:grid-cols-[1.6fr_1fr] md:gap-16">
            <div class="about-story max-w-prose">
                {{-- POSITIESTATEMENT — standard intro treatment (the lead
                     variant read as a bold wall; dropped 2026-07-04, Frederik) --}}
                <x-intro-text>
                    <p>{{ __('about.vision_statement_1') }}</p>
                    <p>{{ __('about.vision_statement_2') }}</p>
                </x-intro-text>

                {{-- VIER EISEN — subtitled body text, the parent voices woven
                     between the demands they speak to. --}}
                <section class="about-section">
                    <x-section-heading>{{ __('about.vision_demands_title') }}</x-section-heading>

                    <h3 class="mt-4">{{ __('about.vision_demand1_title') }}</h3>
                    <p>{{ __('about.vision_demand1_body') }}</p>
                    <x-pull-quote variant="marker" :attribution="$visionQuote1?->attribution ?? __('about.vision_quote_fatima_attribution')">
                        {{ $visionQuote1?->quote ?? __('about.vision_quote_fatima') }}
                    </x-pull-quote>

                    <h3 class="mt-4">{{ __('about.vision_demand2_title') }}</h3>
                    <p>{{ __('about.vision_demand2_body') }}</p>
                    <x-pull-quote variant="marker" :attribution="$visionQuote2?->attribution ?? __('about.vision_quote_camille_attribution')">
                        {{ $visionQuote2?->quote ?? __('about.vision_quote_camille') }}
                    </x-pull-quote>

                    <h3 class="mt-4">{{ __('about.vision_demand3_title') }}</h3>
                    <p>{{ __('about.vision_demand3_body') }}</p>

                    <h3 class="mt-4">{{ __('about.vision_demand4_title') }}</h3>
                    <p>{{ __('about.vision_demand4_body') }}</p>

                    <p class="mt-4">{{ __('about.vision_demands_closing') }}</p>
                </section>
            </div>

            {{-- MANIFEST — sticky rail (same info-card grammar as the Pers
                 contact card); its own label does the heading work. --}}
            <div class="flex flex-col gap-4 md:sticky md:top-28">
                <x-info-card :label="__('about.vision_manifest_label')">
                    <p>{{ __('about.vision_manifest_body') }}</p>
                    <x-cta-button :href="asset('downloads/kidical-mass-manifest.pdf')" variant="secondary" icon="download" size="sm" class="mt-2 self-start" target="_blank" rel="noopener noreferrer">
                        {{ __('about.vision_manifest_link') }}
                    </x-cta-button>
                </x-info-card>
            </div>
        </div>
    </section>

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta :heading="__('about.vision_closing_heading')"
            :href="route('about.organisation')" :label="__('about.vision_closing_label')" />
    </x-slot:closing>

</x-layouts::site>
