{{--
    Over ons / Pers — /about/press (P-19)
    Arrange/distill pass 2026-07 (design-choices-pers K1-K5): one white
    two-column section, the year-grouped archive left under 'In de pers',
    a sticky perscontact card right. No separate contact heading, the card
    label does that work. Copy: lang/nl/about.php (press_*). Structure only.
--}}
<x-layouts::site :title="__('nav.press')" :description="__('meta.press')">

    <x-page-hero
        :eyebrow="__('nav.press')"
        :title="__('about.press_title')"
        size="compact">

    <section class="about-section about-section--wide">
        <div class="grid items-start gap-10 md:grid-cols-[1.6fr_1fr] md:gap-16">
            <div>
                @if ($articlesByYear->isNotEmpty())
                    <x-section-heading class="mb-8">{{ __('about.press_overview_title') }}</x-section-heading>
                    <x-press-archive :articles-by-year="$articlesByYear" />
                @else
                    <x-empty-state :heading="__('about.press_empty_title')">
                        {{ __('about.press_empty_body') }}
                    </x-empty-state>
                @endif
            </div>
            <div class="flex flex-col gap-4 md:sticky md:top-28">
                <x-info-card :label="__('about.press_contact_label')">
                    <p>{{ __('about.press_contact_body') }}</p>
                    <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                    <p class="info-card__note">{{ __('about.press_contact_note') }}</p>
                </x-info-card>
                <p class="m-0"><a href="{{ route('about.mission') }}" class="more-link">{{ __('about.press_background_link') }}</a></p>
            </div>
        </div>
    </section>

    </x-page-hero>

</x-layouts::site>
