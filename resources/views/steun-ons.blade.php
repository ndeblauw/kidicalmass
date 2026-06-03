{{--
    Steun Kidical Mass — /steun-ons (P-04)
    Normalized 2026-06-03 to the ride/show layout system (DESIGN.md):
    - HERO reuses .activity-hero* (blue band, daisy, circular illustration, -3° H1, badge).
    - "Wat jouw steun mogelijk maakt" reuses .activity-promises* (sky band, white tilted
      cards, canonical red Flux-icon chips).
    - The ask sits on a contained white section; the page closes on a full-bleed yellow
      CTA band (the movement-scale reassurance + a second Growfunding shot).
    Colour story: blue → sky → white → yellow. Structure only; appearance in app.css.
    Copy: lang/nl/support.php. Plan: docs/wiki/design/30-skeleton/steun-ons.md
--}}
<x-layouts::site :title="__('support.title')">

    @php
        $growfunding = 'https://growfunding.be/'.app()->getLocale().'/projects/kidicalmassbelgique';
    @endphp

    {{-- HERO — poster layout, mirrors the ride/show hero --}}
    <section class="activity-hero steun-hero">

        <img src="{{ asset('img/logo-icon.png') }}" alt="" aria-hidden="true" class="activity-hero__daisy">

        <div class="container mx-auto px-4 activity-hero__inner">

            <div class="activity-hero__copy">
                <h1>{{ __('support.title') }}</h1>
                <p class="steun-hero__lead">{{ __('support.hero_lead') }}</p>
            </div>

            <div class="activity-hero__visual">
                <div class="activity-hero__photo steun-hero__circle">
                    <img src="{{ asset('img/illustrations/kid-on-bike.png') }}" alt="" aria-hidden="true">
                </div>
            </div>

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
            <a href="{{ $growfunding }}" class="steun-cta__btn link-plain" target="_blank" rel="noopener noreferrer">
                {{ __('support.ask_cta') }} →
            </a>
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

</x-layouts::site>
