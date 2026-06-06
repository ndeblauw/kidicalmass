{{--
    Steun Kidical Mass — /steun-ons (P-04)
    Mission-led 2026-06-05: the page argues the cause before the ask.
    - HERO (.page-hero blue band): the cause leads the H1.
    - MISSION (.steun-mission): why this matters, the driver to give.
    - PROOF (.steun-proof): real, sourced impact (numbers from docs/raw/website/*).
    - "Wat jouw steun mogelijk maakt" reuses .activity-promises* (sky band, tilted cards).
    - The ask sits on a contained white section; the page closes on a full-bleed yellow
      CTA band (the movement-scale reassurance + a second Growfunding shot).
    Colour story: blue → white → sky → white → yellow. Structure only; appearance in app.css.
    Copy: lang/nl/support.php. Plan: docs/wiki/design/30-skeleton/steun-ons.md
--}}
<x-layouts::site :title="__('support.title')">

    @php
        $growfunding = 'https://growfunding.be/'.app()->getLocale().'/projects/kidicalmassbelgique';

        // Proof stats rendered as a colour-coded card deck (text-left / deck-right).
        // Order here is the deck's top-to-bottom order; the last card (5.500) rests
        // on top, fully legible. Copy stays in lang; colour + icon are presentation.
        $stats = __('support.proof_stats');
        $proofCards = [
            ['stat' => $stats[2], 'color' => 'red', 'icon' => 'map-pin'],         // 16/19 gemeenten
            ['stat' => $stats[1], 'color' => 'green', 'icon' => 'calendar-days'], // 60+ ritten per jaar
            ['stat' => $stats[0], 'color' => 'blue', 'icon' => 'users'],          // 5.500 kinderen en ouders
        ];
    @endphp

    <x-page-hero :eyebrow="__('support.hero_eyebrow')" :title="__('support.hero_title')" illustration="img/illustrations/crocodile-on-tricycle.png">

    {{-- WAAROM — the mission (the driver to give), contained white --}}
    <section class="steun-mission">
        <h2 class="steun-mission__title">{{ __('support.mission_title') }}</h2>
        <p class="steun-mission__body">{{ __('support.mission_body') }}</p>
    </section>

    {{-- PROOF — real, sourced impact (docs/raw/website/*). Numbers stay honest.
         Text left, stat cards as an overlapping deck right (static list on mobile). --}}
    <section class="steun-proof">
        <div class="steun-proof__intro">
            <h2 class="steun-proof__title">{{ __('support.proof_title') }}</h2>
            <p class="steun-proof__body">{{ __('support.proof_body') }}</p>
            <p class="steun-proof__credit">{{ __('support.proof_press') }} {{ __('support.proof_backers') }}</p>
        </div>

        <div class="steun-proof__deck" role="list">
            @foreach ($proofCards as $card)
                <x-stat-card
                    class="steun-proof__card"
                    role="listitem"
                    :value="$card['stat']['value']"
                    :label="$card['stat']['label']"
                    :icon="$card['icon']"
                    :color="$card['color']" />
            @endforeach
        </div>
    </section>

    {{-- WAT JE STEUN MOGELIJK MAAKT — reuses the promises band --}}
    <section class="activity-promises steun-funds">
        <div class="steun-funds__inner container mx-auto px-4">
            <h2 class="steun-funds__title">{{ __('support.funds_title') }}</h2>
            <ul class="steun-funds__grid" role="list">
                @foreach (__('support.funds') as $fund)
                    <li class="activity-promises__item">
                        <div class="activity-promises__icon-wrap">
                            <flux:icon :name="$fund['icon']" variant="solid" class="activity-promises__icon" aria-hidden="true" />
                        </div>
                        <strong>{{ $fund['title'] }}</strong>
                        <p>{{ $fund['body'] }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- DE VRAAG (primair) + GERUSTSTELLING — contained white --}}
    <section class="steun-ask">
        <div class="steun-ask__card">
            <h2 class="steun-ask__title">{{ __('support.ask_title') }}</h2>
            <p class="steun-ask__body">{{ __('support.ask_body') }}</p>
            <a href="{{ $growfunding }}" class="steun-ask__cta" target="_blank" rel="noopener noreferrer">
                <flux:icon.heart variant="solid" class="steun-ask__cta-icon" aria-hidden="true" />
                {{ __('support.ask_cta') }}
                <flux:icon.arrow-up-right class="steun-ask__cta-ext" aria-hidden="true" />
            </a>
            <p class="steun-ask__where">{{ __('support.ask_where') }}</p>
            <p class="steun-ask__note">{{ __('support.ask_note') }}</p>
            <a href="{{ $growfunding }}" class="steun-ask__tiers" target="_blank" rel="noopener noreferrer">
                {{ __('support.tiers') }}
            </a>
            {{-- One-off path (D-9) intentionally omitted until Leticia confirms the
                 mechanism + IBAN — do not publish a bank number before then. --}}
        </div>
        <aside class="steun-ask__free">
            <h3 class="steun-ask__free-title">{{ __('support.free_title') }}</h3>
            <p class="steun-ask__free-body">{{ __('support.free_body') }}</p>
        </aside>
    </section>

    {{-- CLOSING CTA — full-bleed yellow band (movement scale, no backer count) --}}
    <section class="steun-cta">
        <div class="container mx-auto px-4 steun-cta__inner">
            <h2>{{ __('support.scale') }}</h2>
            <p class="steun-cta__sub">{{ __('support.scale_sub') }}</p>
            <x-cta-button :href="$growfunding" variant="blue" class="link-plain" target="_blank" rel="noopener noreferrer">{{ __('support.ask_cta') }}</x-cta-button>
        </div>
    </section>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        // Opacity-only stagger. The cards' tilt lives in CSS, so we deliberately
        // do not touch transform here (it would flatten them).
        const cards = document.querySelectorAll('.steun-funds .activity-promises__item');
        cards.forEach((card, i) => {
            card.style.opacity = '0';
            card.style.transition = 'opacity 0.45s cubic-bezier(0.25, 1, 0.5, 1)';
            card.style.transitionDelay = `${i * 90}ms`;
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        cards.forEach(card => observer.observe(card));
    });
    </script>
    @endpush

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

    <x-slot:closing>
        <x-closing-cta heading="Zin gekregen om mee te rijden?"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>

</x-layouts::site>
