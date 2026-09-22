{{--
    Over ons / Hoe we werken — /about/organisation (P-17)
    Restructured 2026-07 (spec: 2026-07-03-about-section-content-design.md,
    variant A): intro carries the three-tier story (organigram cut), the
    ho-deal columns became two shared titled-list-blocks, safety lives in the
    duo's text, the callout folded into the intro. Copy: lang/{nl,fr}/about.php
    (organisation_*). Structure only; zero page-specific components.
    FR additions (layout proposals, per "Organisation: French content as a
    layout proposal"): the "Des parcours pensés pour les enfants" section and the
    longer task lists are French-only; the coordinator copy mentioning the third
    coordinator carries "To be confirmed".
--}}
@php
    $isFr = app()->getLocale() === 'fr';
@endphp
<x-layouts::site :title="__('nav.organisation')" :description="__('meta.organisation')">

    <x-page-hero
        :eyebrow="__('nav.organisation')"
        :title="__('about.organisation.title')"
        size="compact">

    {{-- HOE WE GEORGANISEERD ZIJN — lokaal-eerst + no-HQ; the two lists below
         carry the national/local detail (distill 2026-07-04) --}}
    <x-intro-text>
        <p>{{ __('about.organisation.intro_1') }}</p>
        <p>{{ __('about.organisation.intro_2') }}</p>
    </x-intro-text>

    {{-- WIE WAT DOET — the national/local two-sided story as one white panel:
         a single surface with a hairline seam instead of two floating dotted
         lists (simplify 2026-07-07, review follow-up; band dropped 2026-07-08).
         French carries 7 national / 5 local items (vs 4/4 in Dutch); the extra
         "Les groupes gardent leur autonomie" line is French-only. --}}
    <section class="about-section about-section--wide">
        <x-section-heading>{{ __('about.organisation.who.title') }}</x-section-heading>
        <div class="about-who">
            <x-titled-list-block variant="plain" :title="__('about.organisation.national.title')" level="h3">
                @foreach (__('about.organisation.national.items') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </x-titled-list-block>
            <x-titled-list-block variant="plain" :title="__('about.organisation.local.title')" level="h3">
                @foreach (__('about.organisation.local.items') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </x-titled-list-block>
        </div>
        @if ($isFr)
            <p class="mt-4 max-w-prose">{{ __('about.organisation.local.note') }}</p>
        @endif
    </section>

    {{-- HET COÖRDINATIETEAM — carries safety & vorming (they run it); text and
         person cards side by side on desktop (polish 2026-07-04). The French copy
         adds a third coordinator (Alison) to the duo framing — unconfirmed. --}}
    <section class="about-section about-section--wide">
        <x-section-heading>{{ __('about.organisation.duo.title') }}</x-section-heading>
        <div class="grid gap-8 md:grid-cols-[1fr_22rem] md:gap-12">
            <div class="max-w-prose space-y-4">
                @if ($isFr)
                    <x-to-be-confirmed>
                        <p>{{ __('about.organisation.duo.body_1') }}</p>
                    </x-to-be-confirmed>
                @else
                    <p>{{ __('about.organisation.duo.body_1') }}</p>
                @endif
                <p>{{ __('about.organisation.duo.body_2') }}</p>
                @if (filled(__('about.organisation.duo.link')))
                    <p><a href="{{ localized_route('getting-started') }}" class="more-link">{{ __('about.organisation.duo.link') }}</a></p>
                @endif
            </div>
            @if ($teamMembers->isNotEmpty())
                <ul class="about-duo" role="list">
                    @foreach ($teamMembers as $member)
                        <li>
                            <x-person-card
                                :name="$member->name"
                                :role="$member->role"
                                :bio="$member->bio"
                                :photo="$member->getFirstMediaUrl('photo', 'thumb') ?: null" />
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    {{-- ROUTES OP KINDERMAAT — shared section (NL + FR). The FR copy carried it
         first as a layout proposal; the NL translation now lands on the same spot. --}}
    <x-layout-proposal :when="$isFr">
        <section class="about-section about-section--wide">
            <x-section-heading>{{ __('about.organisation.parcours.title') }}</x-section-heading>
            <div class="max-w-prose space-y-4">
                <p>{{ __('about.organisation.parcours.body_1') }}</p>
                <p>{{ __('about.organisation.parcours.body_2') }}</p>
                <p>{{ __('about.organisation.parcours.body_3') }}</p>
                <p><a href="{{ localized_route('getting-started') }}" class="more-link">{{ __('about.organisation.parcours.link') }} →</a></p>
            </div>
        </section>
    </x-layout-proposal>

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta :heading="__('about.organisation.closing.heading')"
            :href="localized_route('volunteer')" :label="__('about.organisation.closing.label')" />
    </x-slot:closing>

</x-layouts::site>