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
        :title="__('about.press.title')"
        size="compact">

    <section class="about-section about-section--wide">
        <div class="grid items-start gap-10 md:grid-cols-[1.6fr_1fr] md:gap-16">
            <div>
                @if ($articlesByYear->isNotEmpty())
                    <x-section-heading class="mb-8">{{ __('about.press.overview.title') }}</x-section-heading>
                    <x-press-archive :articles-by-year="$articlesByYear" />
                @else
                    <x-empty-state :heading="__('about.press.empty.title')">
                        {{ __('about.press.empty.body', ['email' => config('kidicalmass.contact.email')]) }}
                    </x-empty-state>
                @endif
            </div>
            <div class="flex flex-col gap-4 md:sticky md:top-28">
                <x-info-card :label="__('about.press.contact.label')">
                    <p>{{ __('about.press.contact.body') }}</p>
                    <a href="mailto:{{ config('kidicalmass.contact.email') }}" class="info-card__link">{{ config('kidicalmass.contact.email') }}</a>
                    <p class="info-card__note">{{ __('about.press.contact.note') }}</p>
                </x-info-card>
                <p class="m-0"><a href="{{ localized_route('about.mission') }}" class="more-link">{{ __('about.press.background.link') }}</a></p>
            </div>
        </div>
    </section>

    </x-page-hero>

</x-layouts::site>
