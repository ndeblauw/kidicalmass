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
                <ul class="flex flex-wrap gap-x-3 gap-y-3">
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
                    <ul class="flex flex-wrap gap-x-3 gap-y-3">
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

        <div class="mt-12 space-y-12">
            @foreach ($byRegion as $region => $regionGroups)
                <section>
                    <h3 class="grp-region__title">{{ $regionLabels[$region] ?? $region }}</h3>
                    <ul class="flex flex-wrap gap-x-3 gap-y-3">
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

    {{-- Movement counter — moved out of the hero; the scale that sets up the recruiting CTA. --}}
    <dl class="grp-stats">
        <div class="grp-stats__item">
            <dd class="grp-stats__num">{{ $groups->count() }}</dd>
            <dt class="grp-stats__label">lokale {{ $groups->count() === 1 ? 'groep' : 'groepen' }}</dt>
        </div>
        <div class="grp-stats__item">
            <dd class="grp-stats__num">{{ $activityCount }}</dd>
            <dt class="grp-stats__label">activiteiten dit jaar</dt>
        </div>
    </dl>

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta heading="Staat jouw stad er nog niet bij?"
            :href="route('volunteer')" label="Zo begin je" />
    </x-slot:closing>

</x-layouts::site>
