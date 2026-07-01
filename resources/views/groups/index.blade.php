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
                    ->sortBy('zip')
                    ->sortByDesc(fn ($group) => $mineIds->contains($group->id) ? 1 : 0)
                    ->values();
            @endphp

            <div class="grp-finder" data-group-finder data-location='@json($location)'>
                <div class="grp-finder__controls">
                    <div class="grp-finder__picker">
                        <livewire:location-picker :compact="true" />
                    </div>
                    <div class="grp-regions">
                        @if ($location)
                            <button type="button" class="grp-region-btn grp-region-btn--nearby is-active" data-region="nearby">
                                <span class="grp-region-btn__pin" aria-hidden="true"></span>
                                Dichtbij
                            </button>
                        @endif
                        <button type="button" class="grp-region-btn {{ $location ? '' : 'is-active' }}" data-region="all">
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
            :href="route('groups.start')" label="Zo begin je" />
    </x-slot:closing>

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9/dist/leaflet.js"></script>
        <script>
        (function () {
            function haversineKm(aLat, aLng, bLat, bLng) {
                const R = 6371;
                const dLat = ((bLat - aLat) * Math.PI) / 180;
                const dLng = ((bLng - aLng) * Math.PI) / 180;
                const s =
                    Math.sin(dLat / 2) ** 2 +
                    Math.cos((aLat * Math.PI) / 180) * Math.cos((bLat * Math.PI) / 180) * Math.sin(dLng / 2) ** 2;
                return 2 * R * Math.asin(Math.sqrt(s));
            }

            function initFinder() {
                const root = document.querySelector('[data-group-finder]');
                const mapEl = document.getElementById('grp-map');
                if (!root || !mapEl || typeof L === 'undefined') {
                    return;
                }

                const markers = JSON.parse(mapEl.dataset.markers || '[]').filter((m) => m.lat != null && m.lng != null);
                let location = null;
                try {
                    location = JSON.parse(root.dataset.location || 'null');
                } catch (e) {
                    location = null;
                }

                const styles = getComputedStyle(document.documentElement);
                const token = (name, fallback) => styles.getPropertyValue(name).trim() || fallback;
                const regionColor = {
                    'Brussels Capital Region': token('--color-kidical-blue', '#1d67cd'),
                    Wallonia: token('--color-kidical-orange', '#F0803C'),
                    Flanders: token('--color-kidical-green', '#5CB85C'),
                };
                const fallbackColor = token('--color-kidical-red', '#E63A7B');

                const map = L.map(mapEl, { zoomControl: true, scrollWheelZoom: false });
                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    subdomains: 'abcd',
                    maxZoom: 19,
                }).addTo(map);

                const bySlug = {};
                markers.forEach((m) => {
                    const color = regionColor[m.region] || fallbackColor;
                    const icon = L.divIcon({
                        className: '',
                        html: `<span class="grp-pin" data-slug="${m.slug}" style="background:${color}"></span>`,
                        iconSize: [26, 26],
                        iconAnchor: [13, 26],
                        popupAnchor: [0, -24],
                    });
                    const marker = L.marker([m.lat, m.lng], { icon }).addTo(map);
                    marker.bindTooltip(m.name, {
                        permanent: true,
                        interactive: true,
                        direction: 'top',
                        offset: [0, -22],
                        className: 'grp-marker-label',
                    });
                    marker.on('click', () => { window.location = m.url; });
                    bySlug[m.slug] = { marker, data: m };
                });

                const allBounds = markers.length ? L.latLngBounds(markers.map((m) => [m.lat, m.lng])) : null;
                const fitAll = () => allBounds && map.fitBounds(allBounds, { padding: [40, 40] });
                map.whenReady(() => map.invalidateSize());

                const cards = Array.from(root.querySelectorAll('.grp-card'));
                const listEl = root.querySelector('[data-list]');
                const countEl = root.querySelector('[data-count]');
                const statusEl = root.querySelector('[data-status]');
                const pinEl = (slug) => mapEl.querySelector(`.grp-pin[data-slug="${slug}"]`);

                cards.forEach((card) => {
                    const slug = card.dataset.slug;
                    card.addEventListener('mouseenter', () => pinEl(slug)?.classList.add('is-hot'));
                    card.addEventListener('mouseleave', () => pinEl(slug)?.classList.remove('is-hot'));
                });

                // Proximity is only available once a location is set: compute per-group
                // distances, label the cards, and drop a "you are here" marker.
                let dist = null;
                if (location && location.lat != null && location.lng != null && markers.length) {
                    const meIcon = L.divIcon({
                        className: '',
                        html: '<span class="grp-pin grp-pin--me"></span>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 20],
                    });
                    L.marker([location.lat, location.lng], { icon: meIcon }).addTo(map).bindPopup('<strong>Jij bent hier</strong>');

                    dist = {};
                    markers.forEach((m) => {
                        dist[m.slug] = haversineKm(location.lat, location.lng, m.lat, m.lng);
                    });
                }

                function sortByDistance() {
                    cards
                        .slice()
                        .sort((a, b) => (dist[a.dataset.slug] ?? 1e9) - (dist[b.dataset.slug] ?? 1e9))
                        .forEach((c) => listEl.appendChild(c));
                }
                function fitNearest() {
                    const nearest = markers
                        .slice()
                        .sort((a, b) => dist[a.slug] - dist[b.slug])
                        .slice(0, 5);
                    map.fitBounds(L.latLngBounds([[location.lat, location.lng], ...nearest.map((m) => [m.lat, m.lng])]), {
                        padding: [55, 55],
                    });
                }

                const regionButtons = Array.from(root.querySelectorAll('.grp-region-btn'));
                function setRegion(region) {
                    regionButtons.forEach((b) => b.classList.toggle('is-active', b.dataset.region === region));

                    if (region === 'nearby') {
                        cards.forEach((card) => {
                            card.classList.remove('is-hidden');
                            pinEl(card.dataset.slug)?.classList.remove('is-dim');
                        });
                        countEl.textContent = `${cards.length} ${cards.length === 1 ? 'groep' : 'groepen'}`;
                        sortByDistance();
                        fitNearest();
                        statusEl.textContent = 'Dichtst bij jou';
                        return;
                    }

                    let shown = 0;
                    cards.forEach((card) => {
                        const inRegion = region === 'all' || card.dataset.region === region;
                        card.classList.toggle('is-hidden', !inRegion);
                        if (inRegion) shown += 1;
                        pinEl(card.dataset.slug)?.classList.toggle('is-dim', !inRegion);
                    });
                    countEl.textContent = `${shown} ${shown === 1 ? 'groep' : 'groepen'}`;
                    if (region === 'all') {
                        fitAll();
                        statusEl.textContent = 'Heel België';
                    } else {
                        const pts = markers.filter((m) => m.region === region).map((m) => [m.lat, m.lng]);
                        if (pts.length) map.fitBounds(L.latLngBounds(pts), { padding: [55, 55] });
                        const label = markers.find((m) => m.region === region)?.regionLabel || region;
                        statusEl.textContent = label;
                    }
                }
                regionButtons.forEach((b) => b.addEventListener('click', () => setRegion(b.dataset.region)));

                if (dist) {
                    setRegion('nearby');
                } else {
                    fitAll();
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initFinder);
            } else {
                initFinder();
            }
        })();
        </script>
    @endpush

</x-layouts::site>
