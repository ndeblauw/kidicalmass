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
        size="compact">

    {{-- HOE WE GEORGANISEERD ZIJN — lokaal-eerst + no-HQ; the two lists below
         carry the national/local detail (distill 2026-07-04) --}}
    <x-intro-text>
        <p>{{ __('about.organisation_intro_1') }}</p>
        <p>{{ __('about.organisation_intro_2') }}</p>
    </x-intro-text>

    {{-- WIE WAT DOET — the national/local two-sided story as one white panel
         on the sky band: a single surface with a hairline seam instead of two
         floating dotted lists (simplify 2026-07-07, review follow-up) --}}
    <section class="about-band about-band--sky">
        <div class="container mx-auto px-4">
            <x-section-heading class="mb-8">{{ __('about.organisation_who_title') }}</x-section-heading>
            <div class="about-who">
                <x-titled-list-block variant="plain" :title="__('about.organisation_national_title')" level="h3">
                    @foreach (__('about.organisation_national') as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </x-titled-list-block>
                <x-titled-list-block variant="plain" :title="__('about.organisation_local_title')" level="h3">
                    @foreach (__('about.organisation_local') as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </x-titled-list-block>
            </div>
        </div>
    </section>

    {{-- HET COÖRDINATIEDUO — carries safety & vorming (they run it); text and
         person cards side by side on desktop (polish 2026-07-04) --}}
    <section class="about-section about-section--wide">
        <x-section-heading>{{ __('about.organisation_duo_title') }}</x-section-heading>
        <div class="grid gap-8 md:grid-cols-[1fr_22rem] md:gap-12">
            <div class="max-w-prose space-y-4">
                <p>{{ __('about.organisation_duo_body_1') }}</p>
                <p>{{ __('about.organisation_duo_body_2') }}</p>
                <p><a href="{{ route('getting-started') }}" class="more-link">{{ __('about.organisation_duo_link') }}</a></p>
            </div>
            @if ($teamMembers->isNotEmpty())
                <ul class="about-duo" role="list">
                    @foreach ($teamMembers as $member)
                        <li>
                            <x-person-card
                                :name="$member->name"
                                :role="$member->role"
                                :bio="$member->bio_nl"
                                :photo="$member->getFirstMediaUrl('photo', 'thumb') ?: null" />
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta :heading="__('about.organisation_closing_heading')"
            :href="route('volunteer')" :label="__('about.organisation_closing_label')" />
    </x-slot:closing>

</x-layouts::site>
