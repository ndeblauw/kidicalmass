{{--
    Steun Kidical Mass — /steun-ons (P-04)
    Mission-led 2026-06-05: the page argues the cause before the ask.
    - HERO (.page-hero blue band): the cause leads the H1.
    - MISSION (.steun-mission): why this matters, the driver to give.
    - PROOF (.steun-proof): real, sourced impact (numbers from docs/raw/website/*).
    - "Wat jouw steun mogelijk maakt" = green-check checklist (x-titled-list-block) left, organiser collage right.
    - The ask sits on a contained white section; the page closes on a full-bleed yellow
      CTA band (the movement-scale reassurance + a second Growfunding shot).
    Colour story: blue → white → sky → white → yellow. Structure only; appearance in app.css.
    Copy: lang/nl/support.php. Plan: docs/wiki/design/30-skeleton/steun-ons.md
--}}
<x-layouts::site :title="__('support.title')" :description="__('meta.support')">

    @php
        $growfunding = 'https://growfunding.be/'.app()->getLocale().'/projects/kidicalmassbelgique';

        // $proofCards is computed live (App\Support\SupportStats) and passed in by
        // the route: local groups + rides are counted from the database, the
        // participant figure comes from the curated year_stats row. Empty metrics
        // are already filtered out, so the deck renders only honest cards (1-3).
        // Order is bottom-to-top: the last card rests on top of the stack, legible.
    @endphp

    <x-page-hero :eyebrow="__('support.hero.eyebrow')" :title="__('support.hero.title')" illustration="img/illustrations/heart-sign-holder.svg">

        {{-- High-intent shortcut: the ask sits in the hero so a ready-to-give
             visitor never has to scroll the full argument to find the door. --}}
        <x-slot:controls>
            <div class="steun-hero__cta">
                <x-cta-button :href="$growfunding" variant="yellow" icon="heart" disc="red"
                    target="_blank" rel="noopener noreferrer" class="link-plain">{{ __('support.ask.cta') }}</x-cta-button>
                <p class="steun-hero__cta-note">{{ __('support.hero.cta_note') }}</p>
            </div>
        </x-slot:controls>

    {{-- STORY — proof + the load under ONE convincing title. The mission lead and
         the story share one text column; the sourced stat deck sits top-aligned on
         the right (the scale), the work chips below the body (the load behind it).
         Numbers stay honest (docs/raw/website/*). Deck is a static list on mobile. --}}
    <section class="steun-story">
        <div class="steun-story__text">
            {{-- Mission lead: an intro-scale opener (the hero already carries the title). --}}
            <p class="steun-mission__body">{{ __('support.mission.body') }}</p>

            <div class="steun-story__intro">
                <h2 class="steun-story__title">{{ __('support.story.title') }}</h2>
                <p class="steun-story__body">{{ __('support.story.body') }}</p>
                {{-- The team's work, as a flowing second paragraph. --}}
                <p class="steun-story__body">{{ __('support.story.work') }}</p>
            </div>
        </div>

        @if (count($proofCards))
            <div class="steun-proof__deck" role="list">
                @foreach ($proofCards as $card)
                    <x-stat-card
                        class="steun-proof__card"
                        role="listitem"
                        :value="$card['value']"
                        :label="$card['label']"
                        :color="$card['color']" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- WAT JE STEUN MOGELIJK MAAKT — content left, a warm collage of the
         organisers right (PAT-20 standalone collage): the people you're backing.
         The French list carries 7 titled items (vs NL's 4 short lines), each
         with a "To be confirmed" marker — the client still has to confirm what
         support money pays for. --}}
    <section class="steun-funds">
        <div class="steun-funds__inner">
            <x-to-be-confirmed :when="filled(__('support.funds_review'))">
                <x-titled-list-block :title="__('support.funds.title')" variant="get" level="h2">
                    @foreach (__('support.funds.items') as $fund)
                        @if (is_array($fund))
                            <li><strong>{{ $fund['title'] }}</strong> — {{ $fund['body'] }}</li>
                        @else
                            <li>{{ $fund }}</li>
                        @endif
                    @endforeach
                </x-titled-list-block>
            </x-to-be-confirmed>

            @php
                // The two organisers the visitor is backing: posing + the team in action.
                $fundPhotos = [
                    ['src' => 'img/photography/ride-trio-pink-vest-lei-portrait.webp', 'alt' => __('support.photos.org_1')],
                    ['src' => 'img/photography/team-blue-sweatshirts-celebration.webp', 'alt' => __('support.photos.org_2')],
                ];
            @endphp
            <x-photo-collage class="steun-funds__collage" :photos="$fundPhotos" />
        </div>
    </section>

    {{-- Proof deck — one-shot reveal: the cards fly up into the stacked deck the
         first time the section scrolls into view (lg+ only, mirrors getting-started). --}}
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        if (!window.matchMedia('(min-width: 1024px)').matches) return;

        const deck = document.querySelector('.steun-proof__deck');
        if (!deck) return;

        deck.classList.add('steun-proof__deck--anim');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    deck.classList.add('is-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        observer.observe(deck);
    });
    </script>
    @endpush

    </x-page-hero>

    {{-- CLOSING — this band IS the page's single ask: it renders outside the white
         panel, flush to the footer. The €3 framing, the t-shirt, the disclaimer and
         all live here (the duplicate white card was removed). No
         ride-oriented closing CTA: it would split intent at the decision.
         The one-off donation returns on /fr (D-9, dropped 2026-07-03) as a marked
         placeholder — the client still has to confirm whether to accept one-off
         gifts and which IBAN to use. The block is shown only where the copy is
         filled (empty in nl), so it is translation-driven, not locale-coded. --}}
    <x-slot:closing>
        <section class="steun-cta">
            <div class="container mx-auto px-4 steun-cta__inner">
                <h2>{{ __('support.ask.title') }}</h2>
                <p class="steun-cta__sub">{{ __('support.ask.body') }}</p>
                <x-cta-button :href="$growfunding" variant="blue" class="link-plain" target="_blank" rel="noopener noreferrer">{{ __('support.ask.button') }}</x-cta-button>
                <p class="steun-cta__note">{{ __('support.ask.note') }}</p>

                @if (filled(__('support.donation.heading')))
                    <x-to-be-confirmed>
                        <div class="steun-donation mt-8">
                            <h3>{{ __('support.donation.heading') }}</h3>
                            <p>{{ __('support.donation.body') }}</p>
                            <p class="steun-donation__iban">{{ __('support.donation.iban') }}</p>
                        </div>
                    </x-to-be-confirmed>
                @endif
            </div>
        </section>
    </x-slot:closing>

</x-layouts::site>
