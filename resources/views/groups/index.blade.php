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

    {{-- Intro lead — full width, first thing in the white panel (matches the
         about-page intro treatment via the shared component). --}}
    <x-intro-text>
        <p>In elke gemeente trekken buren samen de straat op voor veilig fietsen met kinderen. Eén beweging, lokaal geworteld en het hele jaar door actief in jouw buurt.</p>
    </x-intro-text>

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

        {{-- Two columns on lg: the find-tool leads on the left, the movement-scale
             cards ride along as a sticky rail on the right. --}}
        <div class="grp-directory">
            <div class="grp-directory__main">
                <header class="space-y-1">
                    <h2 class="grp-find__title">Vind je groep</h2>
                    <p class="grp-find__sub">Tik je gemeente aan voor de volgende fietstocht en het lokale team.</p>
                </header>

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
                                        <a href="{{ route('groups.show', $row['item']) }}" class="grp-pill link-plain">{{ $row['item']->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="grp-find__sub">Nog geen groep vlak bij jou. Misschien start jij er een?</p>
                        @endif
                    </section>
                @endif

                <div class="mt-10 space-y-10">
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
            </div>

            {{-- Movement scale — the numbers that set up the recruiting CTA. --}}
            <aside class="grp-directory__aside">
                <h2 class="grp-scale__title">Samen zijn we al groot</h2>
                <div class="grp-scale__cards">
                    <x-stat-card
                        :value="$groups->count()"
                        :label="'lokale '.($groups->count() === 1 ? 'groep' : 'groepen')"
                        icon="users"
                        color="blue" />
                    <x-stat-card
                        :value="$activityCount"
                        label="activiteiten dit jaar"
                        icon="calendar-days"
                        color="green" />
                </div>
            </aside>
        </div>
    @else
        <p class="kal-empty mt-10">Er zijn nog geen lokale groepen om te tonen.</p>
    @endif

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta heading="Staat jouw stad er nog niet bij?"
            :href="route('volunteer')" label="Zo begin je" />
    </x-slot:closing>

</x-layouts::site>
