{{--
    Over ons / Wat we doen — /about/mission (P-15)
    Restructured 2026-07 to the Steun-ons pattern (spec: 2026-07-03-about-section-
    content-design.md, variant A): one story column (intro + welkom + quote) with
    the live stat deck beside it, the three axes, and a closing CTA chained to
    Wat we vragen. Copy: lang/nl/about.php (mission_*). Structure only.
--}}
<x-layouts::site :title="__('nav.mission')" :description="__('meta.mission')">

    <x-page-hero
        :eyebrow="__('nav.mission')"
        :title="__('about.mission_title')"
        illustration="img/illustrations/rider-with-flag.svg">

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
                <p class="about-section__link"><a href="{{ route('getting-started') }}">{{ __('about.mission_welcome_link') }}</a></p>
            </section>

            <x-pull-quote :attribution="__('about.mission_quote_attribution')">
                {{ __('about.mission_quote') }}
            </x-pull-quote>
        </div>

        <div class="grid content-start gap-4" role="list" data-stats-source="about-stats">
            @foreach (app(\App\Support\AboutStats::class)->cards() as $card)
                <x-stat-card role="listitem" :value="$card['value']" :label="$card['label']" :color="$card['color']" />
            @endforeach
        </div>
    </section>

    {{-- DRIE DINGEN DIE WE DOEN — unchanged axes on the sky band --}}
    <section class="about-band about-band--sky">
        <div class="container mx-auto px-4">
            <h2 class="about-band__title">{{ __('about.mission_axes_title') }}</h2>
            <ul class="about-card-grid" role="list">
                <li>
                    <x-feature-card icon="rocket-launch" color="red" :title="__('about.mission_axis1_title')">
                        {{ __('about.mission_axis1_body') }}
                    </x-feature-card>
                </li>
                <li>
                    <x-feature-card icon="lifebuoy" color="red" :title="__('about.mission_axis2_title')">
                        {{ __('about.mission_axis2_body') }}
                    </x-feature-card>
                </li>
                <li>
                    <x-feature-card icon="megaphone" color="red" :title="__('about.mission_axis3_title')">
                        {{ __('about.mission_axis3_body') }} <a href="{{ route('about.vision') }}">{{ __('about.mission_axis3_link') }}</a>
                    </x-feature-card>
                </li>
            </ul>
        </div>
    </section>

    @push('scripts')
    <x-about-reveal selector=".about-band .about-card-grid > li" />
    @endpush

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta :heading="__('about.mission_closing_heading')"
            :href="route('about.vision')" :label="__('about.mission_closing_label')" />
    </x-slot:closing>

</x-layouts::site>
