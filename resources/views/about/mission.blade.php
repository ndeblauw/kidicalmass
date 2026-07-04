{{--
    Over ons / Wat we doen — /about/mission (P-15)
    Restructured 2026-07 to the Steun-ons pattern (spec: 2026-07-03-about-section-
    content-design.md, variant A): one story column (intro + welkom + quote +
    the three axes as subtitled body text) with the live stat deck beside it,
    and a closing CTA chained to Wat we vragen. The axes lost their sky band +
    feature-cards 2026-07-04 (design-choices pick: variant C met subtitels).
    Copy: lang/nl/about.php (mission_*). Structure only.
--}}
<x-layouts::site :title="__('nav.mission')" :description="__('meta.mission')">

    <x-page-hero
        :eyebrow="__('nav.mission')"
        :title="__('about.mission_title')"
        size="compact">

    {{-- STORY — intro, welkom and the parent voice as ONE column; the live
         AboutStats deck sits beside it (Steun-ons stramien). --}}
    <section class="grid gap-10 lg:grid-cols-[1fr_20rem] lg:gap-14">
        <div class="max-w-prose">
            <x-intro-text>
                <p>{{ __('about.mission_intro_1') }}</p>
                <p>{{ __('about.mission_intro_2') }}</p>
            </x-intro-text>

            <section class="about-section">
                <x-section-heading>{{ __('about.mission_welcome_title') }}</x-section-heading>
                <p>{{ __('about.mission_welcome_body') }}</p>
                <p><a href="{{ route('getting-started') }}" class="more-link">{{ __('about.mission_welcome_link') }}</a></p>
            </section>

            <x-pull-quote variant="column" :attribution="$missionQuote?->attribution ?? __('about.mission_quote_attribution')">
                {{ $missionQuote?->quote ?? __('about.mission_quote') }}
            </x-pull-quote>

            {{-- DRIE DINGEN DIE WE DOEN — the axes continue the story as
                 subtitled body text (h3 + paragraph per axis). --}}
            <section class="about-section">
                <x-section-heading>{{ __('about.mission_axes_title') }}</x-section-heading>
                <h3 class="mt-4">{{ __('about.mission_axis1_title') }}</h3>
                <p>{{ __('about.mission_axis1_body') }}</p>
                <h3 class="mt-4">{{ __('about.mission_axis2_title') }}</h3>
                <p>{{ __('about.mission_axis2_body') }}</p>
                <h3 class="mt-4">{{ __('about.mission_axis3_title') }}</h3>
                <p>{{ __('about.mission_axis3_body') }}</p>
            </section>
        </div>

        <div class="grid content-start gap-4" role="list" data-stats-source="about-stats">
            @foreach (app(\App\Support\AboutStats::class)->cards() as $card)
                <x-stat-card role="listitem" :value="$card['value']" :label="$card['label']" :color="$card['color']" />
            @endforeach
        </div>
    </section>

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta :heading="__('about.mission_closing_heading')"
            :href="route('about.vision')" :label="__('about.mission_closing_label')" />
    </x-slot:closing>

</x-layouts::site>
