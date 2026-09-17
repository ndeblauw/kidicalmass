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
        :title="__('about.mission.title')"
        size="compact">

    {{-- STORY — intro, welkom and the parent voice as ONE column; the live
         AboutStats deck sits beside it (Steun-ons stramien). --}}
    <section class="grid gap-10 lg:grid-cols-[1fr_20rem] lg:gap-14">
        <div class="about-story max-w-prose">
            <x-intro-text>
                <p>{{ __('about.mission.intro_1') }}</p>
                <p>{{ __('about.mission.intro_2') }}</p>
            </x-intro-text>

            <section class="about-section">
                <x-section-heading>{{ __('about.mission.welcome.title') }}</x-section-heading>
                <p>{{ __('about.mission.welcome.body') }}</p>
                <p><a href="{{ localized_route('getting-started') }}" class="more-link">{{ __('about.mission.welcome.link') }}</a></p>
            </section>

            <x-pull-quote variant="marker" :attribution="$missionQuote?->attribution ?? __('about.mission.quote.attribution')">
                {{ $missionQuote?->quote ?? __('about.mission.quote.text') }}
            </x-pull-quote>

            {{-- DRIE DINGEN DIE WE DOEN — the axes continue the story as
                 subtitled body text (h3 + paragraph per axis). --}}
            <section class="about-section">
                <x-section-heading>{{ __('about.mission.axes.title') }}</x-section-heading>
                <h3 class="mt-4">{{ __('about.mission.axes.item_1.title') }}</h3>
                <p>{{ __('about.mission.axes.item_1.body') }}</p>
                <h3 class="mt-4">{{ __('about.mission.axes.item_2.title') }}</h3>
                <p>{{ __('about.mission.axes.item_2.body') }}</p>
                <h3 class="mt-4">{{ __('about.mission.axes.item_3.title') }}</h3>
                <p>{{ __('about.mission.axes.item_3.body') }}</p>
            </section>
        </div>

        <div class="grid grid-cols-2 content-start gap-4 lg:grid-cols-1" role="list" data-stats-source="about-stats">
            @foreach (app(\App\Support\AboutStats::class)->cards() as $card)
                <x-stat-card role="listitem" :value="$card['value']" :label="$card['label']" :color="$card['color']" />
            @endforeach
        </div>
    </section>

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta :heading="__('about.mission.closing.heading')"
            :href="localized_route('about.vision')" :label="__('about.mission.closing.label')" />
    </x-slot:closing>

</x-layouts::site>
