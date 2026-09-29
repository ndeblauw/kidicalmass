{{--
    Getting Started — "Voor het eerst mee"
    Surface pass 2026-06-04 (Frederik-guided); scroll-stacking deck dropped 2026-09-29.
    - HERO reuses x-page-hero (fixed blue, waving-rider illustration).
    - "Wat je mag verwachten": intro + photo collage left, a static column of tilted
      cards right on desktop; one column on mobile. Section has lead text + 6 big cards with colour-varied
      icon chips. The panel background is light-blue so the rounded seam flows into the sky.
    - FAQ kept as the accordion (contained).
    - CTA is a full-bleed yellow band.
    Structure only; appearance lives in app.css.
--}}
<x-layouts::site :title="__('getting-started.title')" :description="__('meta.getting_started')">

    <x-page-hero
        :eyebrow="__('getting-started.hero.eyebrow')"
        :title="__('getting-started.hero.title')"
        illustration="img/illustrations/waving-rider.svg">

    {{-- WAT JE MAG VERWACHTEN — intro + collage left, tilted card stack right (desktop) --}}
    <section class="gs-expect">
        {{-- Outline only: the cards are h3s, so this keeps h1 → h2 → h3 intact. --}}
        <h2 class="sr-only">{{ __('getting-started.expect.sr_only') }}</h2>
        <div class="gs-expect-layout">

            <div class="gs-expect-left">
                <x-intro-text>
                    <p>{{ __('getting-started.expect.lead') }}</p>
                </x-intro-text>
                @php
                    // Placement (scatter, sizing, FAQ tuck) lives in getting-started.css
                    // so it can adapt per breakpoint; here we only choose the photos.
                    $expectPhotos = array_map(fn (array $photo) => [
                        'src' => $photo['src'],
                        'alt' => $photo['alt'],
                    ], __('getting-started.expect.photos'));
                    $morePhotos = array_map(fn (array $photo) => [
                        'src' => $photo['src'],
                        'alt' => $photo['alt'],
                    ], __('getting-started.expect.photos_more'));
                @endphp
                <x-photo-collage
                    class="gs-expect-collage"
                    :photos="$expectPhotos" />
                {{-- Desktop only: on mobile the columns stack and six photos would push the cards too far down. --}}
                <x-photo-collage
                    class="gs-expect-collage gs-expect-collage--more"
                    :photos="$morePhotos" />
            </div>

            <div class="gs-expect-right">
                <div class="gs-expect-cards">

                    <x-feature-card class="gs-expect-card" icon="clock" color="red" :title="__('getting-started.expect.cards.0.title')">
                        {{ __('getting-started.expect.cards.0.body') }}
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="musical-note" color="blue" :title="__('getting-started.expect.cards.1.title')">
                        {{ __('getting-started.expect.cards.1.body') }}
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="map-pin" color="orange" :title="__('getting-started.expect.cards.2.title')">
                        {{ __('getting-started.expect.cards.2.body') }}
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="ticket" color="ink" :title="__('getting-started.expect.cards.3.title')">
                        {{ __('getting-started.expect.cards.3.body') }}
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="users" color="violet" :title="__('getting-started.expect.cards.4.title')">
                        {{ __('getting-started.expect.cards.4.body') }}
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="shield-check" color="coral" :title="__('getting-started.expect.cards.5.title')">
                        {{ __('getting-started.expect.cards.5.body') }}
                    </x-feature-card>

                </div>
            </div>

        </div>
    </section>

    {{-- VEELGESTELDE VRAGEN — accordion left + illustration riding in from the right --}}
    <section class="gs-faq-section">
        <div class="gs-faq-layout">

        <div class="gs-faq-content">
        <h2 class="gs-section__title">{{ __('getting-started.faq.title') }}</h2>

        <x-faq>
            <x-faq.item :question="__('getting-started.faq.items.0.question')">
                <p>{{ __('getting-started.faq.items.0.answer') }}</p>
            </x-faq.item>
            <x-faq.item :question="__('getting-started.faq.items.1.question')">
                <p>{{ __('getting-started.faq.items.1.answer') }}</p>
            </x-faq.item>
            <x-faq.item :question="__('getting-started.faq.items.2.question')">
                <p>{{ __('getting-started.faq.items.2.answer') }}</p>
            </x-faq.item>
            <x-faq.item :question="__('getting-started.faq.items.3.question')">
                <p>{{ __('getting-started.faq.items.3.answer') }}</p>
            </x-faq.item>
            <x-faq.item :question="__('getting-started.faq.items.4.question')">
                <p>{{ __('getting-started.faq.items.4.answer') }}</p>
            </x-faq.item>
            <x-faq.item :question="__('getting-started.faq.items.5.question')">
                <p>{{ __('getting-started.faq.items.5.answer') }}</p>
            </x-faq.item>
            <x-faq.item :question="__('getting-started.faq.items.6.question')">
                <p>{{ __('getting-started.faq.items.6.answer') }}</p>
            </x-faq.item>
            <x-faq.item :question="__('getting-started.faq.items.7.question')">
                <p>{{ __('getting-started.faq.items.7.answer') }}</p>
            </x-faq.item>
            {{-- id is a redirect target: old Wix /help-je-n-ai-pas-de-vélo lands here (26-redirect-map). --}}
            <x-faq.item id="no-bike" :question="__('getting-started.faq.items.8.question')">
                <p>{{ __('getting-started.faq.items.8.answer') }}</p>
            </x-faq.item>
            <x-faq.item :question="__('getting-started.faq.items.9.question')">
                <p>{{ __('getting-started.faq.items.9.answer') }}</p>
            </x-faq.item>
        </x-faq>
        </div>{{-- /gs-faq-content --}}

        <div class="gs-faq-illustration">
            <img src="{{ asset('img/illustrations/relaxed-rider.svg') }}" alt="" aria-hidden="true" loading="lazy">
        </div>

        </div>{{-- /gs-faq-layout --}}
    </section>

    @push('scripts')
    {{-- FAQ illustration rides in from the right when it scrolls into view --}}
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const illustration = document.querySelector('.gs-faq-illustration');
        if (!illustration) return;

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            illustration.classList.add('is-in');
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    illustration.classList.add('is-in');
                    observer.disconnect();
                }
            });
        }, { threshold: 0.25 });

        observer.observe(illustration);
    });
    </script>
    @endpush

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta :heading="__('getting-started.closing.heading')"
            :href="localized_route('activities.index')" :label="__('getting-started.closing.label')" />
    </x-slot:closing>

</x-layouts::site>
