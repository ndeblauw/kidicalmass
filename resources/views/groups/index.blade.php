{{--
    Lokale groepen (P-10) — national directory.
    Surface pass (distilled): slim .index-hero; list-FIRST region directory (the working
    find-tool) with a slim "map coming soon" note; calm contained "begin een groep" CTA.
    Structure only; appearance in app.css.
    Plan: docs/wiki/design/30-skeleton/chapters.md
--}}
<x-layouts::site title="Lokale groepen">

    {{-- HEADER — slim blue band (directory page; the list is the point) --}}
    <section class="index-hero">
        <img src="{{ asset('img/logo-icon.png') }}" alt="" aria-hidden="true" class="index-hero__daisy">

        <div class="container mx-auto px-4 index-hero__inner">
            <span class="grp-hero__badge">
                <flux:icon.users variant="solid" aria-hidden="true" />
                Lokaal georganiseerd
            </span>
            <h1>Lokale groepen</h1>
            <p class="grp-hero__lead">
                {{ $groups->count() }} lokale {{ $groups->count() === 1 ? 'groep' : 'groepen' }} in heel België.
            </p>
        </div>
    </section>

    @if ($groups->isNotEmpty())
        @php
            // Region = the invisible parent group. Order: Brussel → Wallonië → Vlaanderen.
            $regionOrder = ['Brussels Capital Region', 'Wallonia', 'Flanders'];
            $regionLabels = [
                'Brussels Capital Region' => 'Brussel',
                'Wallonia' => 'Wallonië',
                'Flanders' => 'Vlaanderen',
            ];
            $byRegion = $groups
                ->groupBy(fn ($group) => $group->parent?->name ?? 'Overige groepen')
                ->sortBy(function ($groupsInRegion, $region) use ($regionOrder) {
                    $position = array_search($region, $regionOrder, true);

                    return $position === false ? 99 : $position;
                });
        @endphp

        {{-- DIRECTORY first — the working find-tool --}}
        <section class="mt-10 space-y-1">
            <h2 class="grp-find__title">Vind je groep</h2>
            <p class="grp-find__sub">Tik je gemeente aan voor de volgende fietstocht en het lokale team.</p>
            <p class="grp-map-note">
                <flux:icon.map variant="solid" aria-hidden="true" />
                Een kaart met alle groepen komt binnenkort.
            </p>
        </section>

        <div class="mt-6 space-y-8">
            @foreach ($byRegion as $region => $regionGroups)
                <section>
                    <h3 class="grp-region__title">{{ $regionLabels[$region] ?? $region }}</h3>
                    <ul class="flex flex-wrap gap-2.5">
                        @foreach ($regionGroups->sortBy('name') as $group)
                            <li>
                                <a href="{{ route('groups.show', $group) }}" class="grp-pill link-plain">{{ $group->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endforeach
        </div>
    @else
        <p class="kal-empty mt-10">Er zijn nog geen lokale groepen om te tonen.</p>
    @endif

    {{-- CLOSING CTA — calm contained card (recruiting is the quiet last beat) --}}
    <section class="grp-cta">
        <div class="grp-cta__inner">
            <h2>Staat jouw stad er nog niet bij?</h2>
            <p class="grp-cta__sub">Er komen steeds nieuwe groepen bij. Je hoeft geen fietsexpert te zijn. Gewoon iemand die zijn buurt graag ziet. Wij helpen je op weg.</p>
            <a href="{{ route('volunteer') }}" class="grp-cta__btn link-plain">Zo begin je →</a>
        </div>
    </section>

</x-layouts::site>
