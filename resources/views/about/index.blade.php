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
        ['href' => route('about.mission'), 'icon' => 'flag', 'title' => __('nav.mission'), 'desc' => 'Fietsparades, lokale groepen en de weg naar veilige straten.'],
        ['href' => route('about.vision'), 'icon' => 'eye', 'title' => __('nav.vision'), 'desc' => 'Vier duidelijke vragen aan steden en gemeenten.'],
        ['href' => route('about.organisation'), 'icon' => 'building-office-2', 'title' => __('nav.organisation'), 'desc' => 'Lokaal geworteld, licht gecoördineerd, gedragen door vrijwilligers.'],
        ['href' => route('articles.index'), 'icon' => 'newspaper', 'title' => __('nav.news'), 'desc' => 'Nieuwe afdelingen, mijlpalen en verhalen van onderweg.'],
    ];
    $exitItems = [
        ['href' => route('volunteer'), 'label' => 'Een groep starten of meehelpen'],
        ['href' => route('about.press'), 'label' => 'Ik ben pers'],
        ['href' => route('about.partners'), 'label' => 'Partner of sponsor worden'],
        ['href' => route('membership'), 'label' => 'De beweging steunen'],
    ];
@endphp
<x-layouts::site title="Over ons" :description="__('meta.about')">

    <x-page-hero
        eyebrow="Over ons"
        title="Samen maken we straten voor kinderen."
        illustration="img/illustrations/cyclist-peace-sign.svg">

    {{-- Lead, relocated onto the panel (the hub has no separate intro section). --}}
    <x-intro-text>
        <p>Kidical Mass organiseert fietsparades voor gezinnen in heel België en pleit voor kindvriendelijke straten. Een vrijwilligersnetwerk, lokaal geworteld en samen gecoördineerd.</p>
    </x-intro-text>

    {{-- ACT-EXITS — intention triage as a quiet link row: the exits deciders
         came for stay first, without competing with the browse menu below. --}}
    <nav class="about-exits" aria-label="Meteen iets regelen">
        <p class="about-exits__lead">Meteen iets regelen?</p>
        <ul class="about-exits__list" role="list">
            @foreach ($exitItems as $exit)
                <li><a href="{{ $exit['href'] }}" class="more-link">{{ $exit['label'] }} →</a></li>
            @endforeach
        </ul>
    </nav>

    {{-- SUBPAGINA'S — the browse path as a hairline table of contents. --}}
    <section class="about-section about-section--wide">
        <x-section-heading>Lees meer over de beweging</x-section-heading>
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
        <x-closing-cta heading="Rij mee met de buurt"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>

</x-layouts::site>
