{{--
    Een lokale groep starten (P-?? — new, planned 2026-06-15)
    The canonical "start a local group" page. Replaces the mailto:bike@ coda on Help
    out + the CTA on Chapters (D-12 "email black hole"). Strategy: dissolve three
    barriers — "te groot een klus" (de deal), "wie steunt mij?" (je staat er niet
    alleen voor + the warm form), "is er animo?" (er is animo / proof) — while being
    honest about the commitment so the team gets fewer, higher-intent leads.
    One intent form, two comfort paths (praten met een trekker / klaar voor contact).
    Out of nav, reached contextually. Plan: docs/wiki/design/30-skeleton/start-een-groep.md
--}}
<x-layouts::site :title="__('start.title')" :description="__('meta.getting_started')">

    <x-page-hero
        :eyebrow="__('start.hero.eyebrow')"
        :title="__('start.hero.title')"
        photo="img/photography/team-kidical-mass.webp"
        :photo-alt="__('start.hero.photo_alt')"
        photo-tilt
        :caption="__('start.hero.caption')">

        {{-- Intro opens the white panel, with the "start" CTA in a right column,
             vertically centred to the intro copy (stacks below it on mobile). --}}
        <div class="sg-intro">
            <x-intro-text>
                <p>{{ __('start.intro') }}</p>
            </x-intro-text>
            <div class="sg-intro__action">
                <x-cta-button href="#start" variant="secondary">{{ __('start.intro_cta') }}</x-cta-button>
            </div>
        </div>

        {{-- DRIE SECTIES — simple alternating two-column lockups (collage + text),
             vertically centred, height driven by the content. Order alternates:
             collage-text, text-collage, collage-text. No sticky, no crossfade. --}}
        <section class="sg-story">
            <div class="sg-story__row">
                <div class="sg-story__collage sg-story__collage--a">
                    <figure class="sg-story__photo sg-story__photo--lead">
                        <x-photo src="img/photography/volunteers-season-launch-meetup.webp"
                                 alt="{{ __('start.story.photos.deal_1') }}" />
                    </figure>
                    <figure class="sg-story__photo sg-story__photo--trail">
                        <x-photo src="img/photography/cargo-bike-mother-two-kids-flag.webp"
                                 alt="{{ __('start.story.photos.deal_2') }}" />
                    </figure>
                </div>
                <div class="sg-story__text">
                    <x-titled-list-block :title="__('start.story.deal.title')" variant="ask" level="h2">
                        @foreach (__('start.story.deal.items') as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </x-titled-list-block>
                </div>
            </div>

            <div class="sg-story__row sg-story__row--reverse">
                <div class="sg-story__collage sg-story__collage--b">
                    <figure class="sg-story__photo sg-story__photo--lead">
                        <x-photo src="img/photography/volunteers-pink-vest-group-cobbles.webp"
                                 alt="{{ __('start.story.photos.help_1') }}" />
                    </figure>
                    <figure class="sg-story__photo sg-story__photo--trail">
                        <x-photo src="img/photography/volunteer-handing-stickers-to-kids.webp"
                                 alt="{{ __('start.story.photos.help_2') }}" />
                    </figure>
                </div>
                <div class="sg-story__text">
                    <x-titled-list-block :title="__('start.story.help.title')" variant="get" level="h2">
                        @foreach (__('start.story.help.items') as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </x-titled-list-block>
                </div>
            </div>

            <div class="sg-story__row">
                <div class="sg-story__collage sg-story__collage--c">
                    <figure class="sg-story__photo sg-story__photo--lead">
                        <x-photo src="img/photography/ride-trio-pink-vest-lei-portrait.webp"
                                 alt="{{ __('start.story.photos.asks_1') }}" />
                    </figure>
                    <figure class="sg-story__photo sg-story__photo--trail">
                        <x-photo src="img/photography/volunteer-pink-vest-blue-helmet.webp"
                                 alt="{{ __('start.story.photos.asks_2') }}" />
                    </figure>
                </div>
                <div class="sg-story__text">
                    <div class="titled-list-block titled-list-block--ask">
                        <h2 class="titled-list-block__title">{{ __('start.story.asks.title') }}</h2>
                        <p class="sg-asks__lead">{{ __('start.story.asks.lead') }}</p>
                        <ul class="sg-asks__list" role="list">
                            @foreach (__('start.story.asks.items') as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- ER IS ANIMO — proof the movement exists: the wide crowd photo beside the
             light-blue animo card. Sits before the FAQ so the visual proof frames the
             practical questions. --}}
        <section class="sg-proof">
            <div class="sg-proof__layout">
                <figure class="sg-proof__photo">
                    <x-photo src="img/photography/ride-park-crowd-cheering-namur.webp"
                             alt="{{ __('start.proof.photo_alt') }}"
                             sizes="(min-width: 768px) 55vw, 92vw" />
                </figure>
                <div class="sg-proof__animo-card">
                    <h2>{{ __('start.proof.title') }}</h2>
                    <p>{{ __('start.proof.body', ['count' => $groupCount]) }}</p>
                    <x-cta-button href="#start" variant="secondary">{{ __('start.proof.cta') }}</x-cta-button>
                </div>
            </div>
        </section>

        {{-- VEELGESTELDE VRAGEN — practical lead objections, after the visual proof.
             Full-bleed with the illustration sliding in from the right, matching the
             getting-started FAQ pattern. --}}
        <section class="sg-faq-section">
            <div class="sg-faq-layout">
                <div class="sg-faq-content">
                    <h2 class="sg-faq__title">{{ __('start.faq.title') }}</h2>
                    <x-faq>
                        <x-faq.item :question="__('start.faq.items.0.question')">
                            <p>{{ __('start.faq.items.0.answer') }}</p>
                        </x-faq.item>
                        <x-faq.item :question="__('start.faq.items.1.question')">
                            <p>{{ __('start.faq.items.1.answer') }}</p>
                        </x-faq.item>
                        <x-faq.item :question="__('start.faq.items.2.question')">
                            <p>{{ __('start.faq.items.2.answer') }}</p>
                        </x-faq.item>
                        <x-faq.item :question="__('start.faq.items.3.question')">
                            <p>{{ __('start.faq.items.3.answer') }}</p>
                        </x-faq.item>
                    </x-faq>
                </div>

                <div class="sg-faq-illustration">
                    <img src="{{ asset('img/illustrations/cargo-bike-family.svg') }}" alt="" aria-hidden="true" loading="lazy">
                </div>
            </div>
        </section>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const illustration = document.querySelector('.sg-faq-illustration');
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

    {{-- INTENT-FORM — the climax. A white form card on the yellow closing band, with
         room around it (no dip). Two columns at desktop: welcome text left, form right.
         The #start anchor catches the hero's "Ik wil starten". --}}
    <x-slot:closing>
        <section class="sg-cta" id="start">
            <div class="container mx-auto px-4">
                <div class="sg-cta__panel">
                    <div class="sg-cta__aside">
                        <img src="{{ asset('img/illustrations/volunteer-with-wrench.svg') }}" alt="" aria-hidden="true" class="sg-cta__mascot">
                        <h2>{{ __('start.closing.title') }}</h2>
                        <p>{{ __('start.closing.body') }}</p>
                    </div>
                    <div class="sg-cta__form-col">
                        <livewire:start-group-enquiry />
                    </div>
                </div>
            </div>
        </section>
    </x-slot:closing>

</x-layouts::site>
