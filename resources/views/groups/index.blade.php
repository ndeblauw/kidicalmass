{{--
    Lokale groepen (P-10) — national directory.
    Surface pass (distilled): slim .index-hero; list-FIRST region directory (the working
    find-tool) with a slim "map coming soon" note; calm contained "begin een groep" CTA.
    Structure only; appearance in app.css.
    Plan: docs/wiki/design/30-skeleton/chapters.md
--}}
<x-layouts::site title="Lokale groepen">

    <x-page-hero
        eyebrow="Lokale groepen"
        title="Jouw buurt fietst al, rij mee."
        illustration="img/illustrations/person-with-boombox.png">

        <x-slot:controls>
            <dl class="grp-hero__stats">
                <div class="grp-hero__stat">
                    <dt class="grp-hero__stat-label">lokale {{ $groups->count() === 1 ? 'groep' : 'groepen' }}</dt>
                    <dd class="grp-hero__stat-num">{{ $groups->count() }}</dd>
                </div>
                <div class="grp-hero__stat">
                    <dt class="grp-hero__stat-label">activiteiten dit jaar</dt>
                    <dd class="grp-hero__stat-num">{{ $activityCount }}</dd>
                </div>
            </dl>
            <div class="grp-hero__locate">
                <livewire:location-picker />
            </div>
        </x-slot:controls>

        <p class="grp-hero__body">
            Kidical Mass is één grote beweging die op vaste momenten samen uitrijdt en het hele jaar door lokaal verschil maakt. In elke gemeente trekt een groep buren de straat op voor veilig fietsen met kinderen.
        </p>

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
        </section>

        @if ($myGroups->isNotEmpty())
            <section class="mt-8">
                <h3 class="grp-region__title">Jouw groep{{ $myGroups->count() > 1 ? 'en' : '' }}</h3>
                <ul class="flex flex-wrap gap-2.5">
                    @foreach ($myGroups as $group)
                        <li><a href="{{ route('groups.show', $group) }}" class="grp-pill grp-pill--mine link-plain">{{ $group->name }}</a></li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($location)
            <section class="mt-8">
                <h3 class="grp-region__title">In de buurt van {{ $location['name'] }}</h3>
                @if ($nearby->isNotEmpty())
                    <ul class="flex flex-wrap gap-2.5">
                        @foreach ($nearby as $row)
                            <li>
                                <a href="{{ route('groups.show', $row['item']) }}" class="grp-pill link-plain">
                                    {{ $row['item']->name }}<span class="grp-pill__km">{{ $row['distance_km'] == 0 ? 'hier' : $row['distance_km'].' km' }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="grp-find__sub">Nog geen groep vlak bij jou. Misschien start jij er een?</p>
                @endif
            </section>
        @endif

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

    </x-page-hero>

</x-layouts::site>
