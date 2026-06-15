{{--
    Lokale groepen (P-10) — list + map finder.
    Server-rendered link list (works without JS) + a markers island that the
    @push('scripts') block turns into a synced Leaflet map. The shared Livewire
    location-picker owns postcode search + geolocation; the map reacts to the
    resolved location on load. Spec: docs/superpowers/specs/2026-06-15-lokale-groepen-list-map-finder-design.md
--}}
<x-layouts::site title="Lokale groepen">

    <x-page-hero
        eyebrow="Lokale groepen"
        title="Jouw buurt fietst al, rij mee."
        illustration="img/illustrations/longtail-with-kid.svg">

        <x-intro-text>
            <p>In elke gemeente trekken buren samen de straat op voor veilig fietsen met kinderen. Eén beweging, lokaal geworteld en het hele jaar door actief in jouw buurt.</p>
        </x-intro-text>

        @if ($groups->isNotEmpty())
            @php
                $regionOrder = ['Brussels Capital Region', 'Wallonia', 'Flanders'];
                $mineIds = $myGroups->pluck('id');
                $orderedGroups = $groups
                    ->sortBy('name')
                    ->sortByDesc(fn ($group) => $mineIds->contains($group->id) ? 1 : 0)
                    ->values();
            @endphp

            <div class="grp-finder" data-group-finder data-location='@json($location)'>
                <div class="grp-finder__controls">
                    <div class="grp-regions">
                        <button type="button" class="grp-region-btn is-active" data-region="all">
                            Heel België <span class="grp-region-btn__count">{{ $groups->count() }}</span>
                        </button>
                        @foreach ($regionOrder as $regionKey)
                            @php $count = $regionCounts[$regionKey] ?? 0; @endphp
                            @if ($count > 0)
                                <button type="button" class="grp-region-btn" data-region="{{ $regionKey }}">
                                    <span class="grp-region-btn__dot" aria-hidden="true"></span>
                                    {{ $regionLabels[$regionKey] ?? $regionKey }}
                                    <span class="grp-region-btn__count">{{ $count }}</span>
                                </button>
                            @endif
                        @endforeach
                    </div>
                    <div class="grp-finder__picker">
                        <livewire:location-picker :compact="true" />
                    </div>
                </div>

                <div class="grp-finder__split">
                    <div class="grp-results">
                        <p class="grp-results__count" data-count>{{ $groups->count() }} {{ $groups->count() === 1 ? 'groep' : 'groepen' }}</p>
                        <ul class="grp-results__list" data-list>
                            @foreach ($orderedGroups as $group)
                                <li class="grp-card {{ $mineIds->contains($group->id) ? 'grp-card--mine' : '' }}"
                                    data-slug="{{ $group->shortname }}"
                                    data-region="{{ $group->parent?->name }}">
                                    <a href="{{ route('groups.show', $group) }}" class="grp-card__link link-plain">
                                        <span class="grp-card__dot" aria-hidden="true"></span>
                                        <span class="grp-card__main">
                                            <span class="grp-card__name">{{ $group->name }}@if ($mineIds->contains($group->id))<span class="grp-card__tag">· jouw groep</span>@endif</span>
                                            <span class="grp-card__zip">{{ $group->zip }}</span>
                                        </span>
                                        <span class="grp-card__dist" data-dist></span>
                                        <span class="grp-card__go" aria-hidden="true">→</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="grp-map-shell">
                        <p class="grp-map__status" data-status>Heel België</p>
                        <div id="grp-map" class="grp-map" data-markers='@json($markers)'></div>
                    </div>
                </div>
            </div>
        @else
            <p class="kal-empty mt-10">Er zijn nog geen lokale groepen om te tonen.</p>
        @endif

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta heading="Staat jouw stad er nog niet bij?"
            :href="route('volunteer')" label="Zo begin je" />
    </x-slot:closing>

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9/dist/leaflet.js"></script>
        {{-- Sync logic (region filter + list/map) is added in a later task, right here. --}}
    @endpush

</x-layouts::site>
