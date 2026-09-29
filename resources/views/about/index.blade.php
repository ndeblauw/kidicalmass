{{--
    Over ons — /about (P-14)
    Built 2026-06-03 to the DESIGN.md kit; lightened 2026-07-07 (variant "geen
    dozen"); reworked after the client meeting of 2026-09-22. A navigational hub
    for "deciders & deepeners": orient ("what's in this section?") and route
    ("where should I go?"). Two AboutStats highlights sit beside the intro (in
    place of a photo), the act-exits are compact link feature cards, the read
    path is a hairline table of contents, and the latest three news items close
    the page above the yellow CTA band (they replace the old "Nieuws" TOC row).
    Plan: docs/wiki/design/30-skeleton/about.md + about-journey.md
--}}
@php
    $readItems = [
        ['href' => localized_route('about.mission'), 'icon' => 'flag', 'title' => __('nav.mission'), 'desc' => __('about.hub.read.descs.0')],
        ['href' => localized_route('about.vision'), 'icon' => 'eye', 'title' => __('nav.vision'), 'desc' => __('about.hub.read.descs.1')],
        ['href' => localized_route('about.organisation'), 'icon' => 'building-office-2', 'title' => __('nav.organisation'), 'desc' => __('about.hub.read.descs.2')],
    ];
    $exitItems = [
        ['href' => localized_route('volunteer'), 'icon' => 'hand-raised', 'color' => 'red'],
        ['href' => localized_route('about.press'), 'icon' => 'megaphone', 'color' => 'blue'],
        ['href' => localized_route('about.partners'), 'icon' => 'building-office', 'color' => 'orange'],
        ['href' => localized_route('membership'), 'icon' => 'heart', 'color' => 'green'],
    ];
@endphp
<x-layouts::site :title="__('about.hub.title')" :description="__('meta.about')">

    <x-page-hero
        :eyebrow="__('about.hub.hero.eyebrow')"
        :title="__('about.hub.hero.title')"
        illustration="img/illustrations/cyclist-peace-sign.svg">

    {{-- Lead, relocated onto the panel (the hub has no separate intro section),
         with two headline numbers beside it in place of a photo (Mission stramien). --}}
    <section class="grid gap-8 lg:grid-cols-[1fr_20rem] lg:gap-14">
        <x-intro-text>
            <p>{{ __('about.hub.intro') }}</p>
        </x-intro-text>

        @if ($stats)
            <div class="grid grid-cols-2 content-start gap-4 lg:grid-cols-1" role="list" data-stats-source="about-stats">
                @foreach ($stats as $card)
                    <x-stat-card role="listitem" :value="$card['value']" :label="$card['label']" :color="$card['color']" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- ACT-EXITS — intention triage as compact link feature cards: the exits
         deciders came for stay first and read as clear destinations. --}}
    <nav class="about-exits" aria-label="{{ __('about.hub.exits.aria') }}">
        <p class="about-exits__lead">{{ __('about.hub.exits.lead') }}</p>
        <ul class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" role="list">
            @foreach ($exitItems as $i => $exit)
                <li class="flex">
                    <x-feature-card size="md" class="w-full" :href="$exit['href']" :icon="$exit['icon']" :color="$exit['color']" :title="__('about.hub.exits.items.'.$i)">
                        {{ __('about.hub.exits.descs.'.$i) }}
                    </x-feature-card>
                </li>
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

    {{-- LAATSTE NIEUWS — the three newest items, in the news-page card grid,
         to show the work behind the scenes. Hidden until something is published. --}}
    @if ($latestArticles->isNotEmpty())
        <section class="about-section about-section--wide" data-about-latest-news>
            <x-section-heading>{{ __('about.hub.news.title') }}</x-section-heading>
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3" data-article-grid>
                @foreach ($latestArticles as $article)
                    <x-article-card :article="$article" />
                @endforeach
            </div>
            <p><a href="{{ localized_route('articles.index') }}" class="more-link">{{ __('about.hub.news.all') }} →</a></p>
        </section>
    @endif

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta :heading="__('about.hub.closing.heading')"
            :href="localized_route('activities.index')" :label="__('about.hub.closing.label')" />
    </x-slot:closing>

</x-layouts::site>
