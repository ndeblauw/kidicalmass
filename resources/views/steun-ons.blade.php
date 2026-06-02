<x-layouts::site :title="__('support.title')">

    @php
        $growfunding = 'https://growfunding.be/'.app()->getLocale().'/projects/kidicalmassbelgique';
        $funds = [
            ['icon' => 'map', 'text' => __('support.funds.meer_rides')],
            ['icon' => 'shield-check', 'text' => __('support.funds.safety')],
            ['icon' => 'sparkles', 'text' => __('support.funds.independent')],
        ];
    @endphp

    {{-- HERO — full-bleed blue band --}}
    <section class="steun-hero">
        <div class="steun-hero__inner container mx-auto px-4">
            <h1 class="steun-hero__title">{{ __('support.title') }}</h1>
            <p class="steun-hero__lead">{{ __('support.hero_lead') }}</p>
        </div>
    </section>

    {{-- WAT JE STEUN MOGELIJK MAAKT --}}
    <section class="steun-funds">
        <h2 class="steun-funds__title">{{ __('support.funds_title') }}</h2>
        <ul class="steun-funds__grid">
            @foreach ($funds as $fund)
                <li class="steun-funds__item">
                    <span class="steun-funds__chip">
                        <flux:icon :name="$fund['icon']" class="steun-funds__icon" aria-hidden="true" />
                    </span>
                    <span class="steun-funds__text">{{ $fund['text'] }}</span>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- DE VRAAG (primair) + GERUSTSTELLING --}}
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
            {{-- One-off path (D-9) is intentionally omitted until Leticia confirms the
                 mechanism + IBAN — do not publish a bank number before then. --}}
        </div>
        <aside class="steun-ask__free">
            <h3 class="steun-ask__free-title">{{ __('support.free_title') }}</h3>
            <p class="steun-ask__free-body">{{ __('support.free_body') }}</p>
        </aside>
    </section>

    {{-- ALLE TIERS --}}
    <section class="steun-tiers">
        <a href="{{ $growfunding }}" class="steun-tiers__link" target="_blank" rel="noopener noreferrer">
            {{ __('support.tiers') }}
            <flux:icon.arrow-up-right class="steun-tiers__ext" aria-hidden="true" />
        </a>
    </section>

    {{-- MOVEMENT SCALE — full-bleed band, closing reassurance (no backer count) --}}
    <section class="steun-scale">
        <div class="steun-scale__inner container mx-auto px-4">
            <p class="steun-scale__text">{{ __('support.scale') }}</p>
        </div>
    </section>

</x-layouts::site>
