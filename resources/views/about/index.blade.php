{{--
    Over ons — /about (P-14)
    Built 2026-06-03 to the DESIGN.md kit; lightened 2026-07-07 (review follow-up,
    variant "geen dozen"). A navigational hub for "deciders & deepeners": orient
    ("what's in this section?") and route ("where should I go?"). The act-exits
    (including Pers + Partners) flow as a quiet link row under the intro; the read
    path is a hairline table of contents with icon chips — no card grids. The
    stats deck lives on Wat we doen (mission) — the hub carries none.
    Plan: docs/wiki/design/30-skeleton/about.md + about-journey.md
--}}
@php
    $readItems = [
        ['href' => localized_route('about.mission'), 'icon' => 'flag', 'title' => __('nav.mission'), 'desc' => __('about.hub.read.descs.0')],
        ['href' => localized_route('about.vision'), 'icon' => 'eye', 'title' => __('nav.vision'), 'desc' => __('about.hub.read.descs.1')],
        ['href' => localized_route('about.organisation'), 'icon' => 'building-office-2', 'title' => __('nav.organisation'), 'desc' => __('about.hub.read.descs.2')],
        ['href' => localized_route('articles.index'), 'icon' => 'newspaper', 'title' => __('nav.news'), 'desc' => __('about.hub.read.descs.3')],
    ];
    $exitItems = [
        ['href' => localized_route('volunteer'), 'label' => __('about.hub.exits.items.0')],
        ['href' => localized_route('about.press'), 'label' => __('about.hub.exits.items.1')],
        ['href' => localized_route('about.partners'), 'label' => __('about.hub.exits.items.2')],
        ['href' => localized_route('membership'), 'label' => __('about.hub.exits.items.3')],
    ];
@endphp
<x-layouts::site :title="__('about.hub.title')" :description="__('meta.about')">

    <x-page-hero
        :eyebrow="__('about.hub.hero.eyebrow')"
        :title="__('about.hub.hero.title')"
        illustration="img/illustrations/cyclist-peace-sign.svg">

    {{-- Lead, relocated onto the panel (the hub has no separate intro section). --}}
    <x-intro-text>
        <p>{{ __('about.hub.intro') }}</p>
    </x-intro-text>

    {{-- ACT-EXITS — intention triage as a quiet link row: the exits deciders
         came for stay first, without competing with the browse menu below. --}}
    <nav class="about-exits" aria-label="{{ __('about.hub.exits.aria') }}">
        <p class="about-exits__lead">{{ __('about.hub.exits.lead') }}</p>
        <ul class="about-exits__list" role="list">
            @foreach ($exitItems as $exit)
                <li><a href="{{ $exit['href'] }}" class="more-link">{{ $exit['label'] }} →</a></li>
            @endforeach
        </ul>
    </nav>

    {{-- SUBPAGINA'S — the browse path as a hairline table of contents. --}}
    <section class="about-section about-section--wide">
        <x-section-heading>{{ __('about.hub.read.title') }}</x-section-heading>
        <ul class="about-toc" role="list">
            @foreach ($readItems as $item)
                <li>
                    <a href="{{ $item['href'] }}" class="link-plain about-toc__item">
                        <x-icon-chip class="about-toc__chip"><flux:icon name="{{ $item['icon'] }}" variant="solid" class="size-6" aria-hidden="true" /></x-icon-chip>
                        <span class="about-toc__text">
                            <span class="about-toc__title">{{ $item['title'] }}</span>
                            <span class="about-toc__desc">{{ $item['desc'] }}</span>
                        </span>
                        <span class="about-toc__arrow" aria-hidden="true">→</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta :heading="__('about.hub.closing.heading')"
            :href="localized_route('activities.index')" :label="__('about.hub.closing.label')" />
    </x-slot:closing>

</x-layouts::site>
