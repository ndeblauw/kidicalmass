<x-layouts::site title="{{ $activity->title_nl }}">

@php($routeCoords = $activity->route_coordinates)
@php($hasMap = count($routeCoords) > 0)
@php($mainImage = $activity->getFirstMedia('main'))

    {{-- HERO — split layout: big copy left, photo right --}}
    <section class="activity-hero">

        <div class="activity-hero__copy">
            <flux:badge color="yellow" variant="solid" class="mb-4">{{ $activity->activity_type->label() }}</flux:badge>

            <h1>{{ $activity->title_nl }}</h1>

            <dl class="activity-hero__meta">
                <div>
                    <dt class="sr-only">Datum en tijd</dt>
                    <dd>
                        <flux:icon.calendar-days aria-hidden="true" />
                        <time datetime="{{ $activity->begin_date->toIso8601String() }}">
                            {{ $activity->begin_date->translatedFormat('l j F · H\hi') }}
                        </time>
                    </dd>
                </div>
                <div>
                    <dt class="sr-only">Locatie</dt>
                    <dd>
                        <flux:icon.map-pin aria-hidden="true" />
                        {{ $activity->location }}
                    </dd>
                </div>
            </dl>

            <div class="activity-hero__actions">
                <flux:button href="{{ route('activities.ical', $activity) }}" icon="calendar-days" variant="primary">
                    + Agenda
                </flux:button>

                <div
                    x-data
                    x-on:click="
                        if (navigator.share) {
                            navigator.share({ title: @js($activity->title_nl), url: window.location.href })
                        } else {
                            navigator.clipboard.writeText(window.location.href)
                            $dispatch('url-copied')
                        }
                    "
                >
                    <flux:button icon="share" variant="ghost">Delen</flux:button>
                    <span
                        class="activity-hero__copied"
                        x-show="false"
                        x-on:url-copied.window="$el.style.display='inline'; setTimeout(() => $el.style.display='none', 2000)"
                    >
                        Gekopieerd!
                    </span>
                </div>
            </div>
        </div>

        @if($mainImage)
            <div class="activity-hero__photo">
                <img src="{{ $mainImage->getUrl() }}"
                     alt="{{ $activity->title_nl }}"
                     class="activity-hero__img">
            </div>
        @endif

    </section>

    {{-- PRACTICAL STRIP --}}
    <ul class="activity-strip" role="list">
        @if($activity->distance)
            <li>{{ $activity->distance }}</li>
            <li aria-hidden="true">·</li>
        @endif
        @if($activity->duration)
            <li>{{ $activity->duration }}</li>
            <li aria-hidden="true">·</li>
        @endif
        <li>Gratis</li>
        <li aria-hidden="true">·</li>
        <li>Geen inschrijving nodig</li>
        <li aria-hidden="true">·</li>
        <li>Alle leeftijden welkom</li>
    </ul>

    {{-- UNIQUE RIDE DESCRIPTION — only shown when admin filled it in --}}
    @if($activity->content_nl)
        <section class="activity-section">
            <p>{!! nl2br(e($activity->content_nl)) !!}</p>
        </section>
    @endif

    {{-- FIXED PROMISES — always shown on every Kidical Mass ride --}}
    <section class="activity-promises">
        <h2>Wat kun je verwachten?</h2>
        <ul class="activity-promises__grid" role="list">
            <li class="activity-promises__item">
                <flux:icon.clock class="activity-promises__icon" aria-hidden="true" />
                <div>
                    <strong>Op het tempo van het jongste kind</strong>
                    <p>We wachten op iedereen. Geen kind blijft achter.</p>
                </div>
            </li>
            <li class="activity-promises__item">
                <flux:icon.shield-check class="activity-promises__icon" aria-hidden="true" />
                <div>
                    <strong>Roze hesjes voor en achter</strong>
                    <p>Vrijwilligers begeleiden de groep en zorgen dat iedereen veilig aankomt.</p>
                </div>
            </li>
            <li class="activity-promises__item">
                <flux:icon.musical-note class="activity-promises__icon" aria-hidden="true" />
                <div>
                    <strong>Muziek onderweg</strong>
                    <p>Een versierde bakfiets met geluid maakt van elke rit een feest.</p>
                </div>
            </li>
            <li class="activity-promises__item">
                <flux:icon.flag class="activity-promises__icon" aria-hidden="true" />
                <div>
                    <strong>Korte verkeersbespreking aan de start</strong>
                    <p>Zodat iedereen weet wat te doen — ook als het je eerste keer is.</p>
                </div>
            </li>
        </ul>
    </section>

    {{-- MAP + CHAPTER/TEAM — two-column below the fold --}}
    @if($hasMap || $activity->groups->isNotEmpty())
        <section class="activity-lower">

            @if($hasMap)
                <div class="activity-map-col">
                    <h2>De route</h2>
                    <div id="activity-map"
                         class="activity-map-embed"
                         data-coordinates="{{ json_encode($routeCoords) }}">
                    </div>
                </div>
            @endif

            <div class="activity-info-col">

                @if($activity->groups->isNotEmpty())
                    <div class="activity-info-col__block">
                        <h2>Onderdeel van</h2>
                        @foreach($activity->groups as $group)
                            <p>
                                <a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a>
                                — elke maand een nieuwe rit door de stad.
                            </p>
                        @endforeach
                    </div>
                @endif

                <div class="activity-info-col__block">
                    <h2>Georganiseerd door</h2>
                    <p>{{ $activity->author->name }} en lokale vrijwilligers.</p>
                    <p><a href="{{ route('home') }}#contact">Wil je meerijden als roze hesje? →</a></p>
                </div>

            </div>

        </section>
    @else
        <section class="activity-section">
            <h2>Georganiseerd door</h2>
            <p>{{ $activity->author->name }} en lokale vrijwilligers.</p>
            <p><a href="{{ route('home') }}#contact">Wil je meerijden als roze hesje? →</a></p>
        </section>
    @endif

    {{-- PHOTO PERMISSION --}}
    <p class="activity-photo-notice">
        Tijdens de rit worden foto's gemaakt. Door deel te nemen ga je akkoord met publicatie.
    </p>

    @if($hasMap)
        @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9/dist/leaflet.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('activity-map');
            if (!el) return;

            const coords = JSON.parse(el.dataset.coordinates || '[]');
            if (!coords.length) return;

            const map = L.map(el, { zoomControl: true, scrollWheelZoom: false });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19,
            }).addTo(map);

            const polyline = L.polyline(coords, {
                color: '#E63A7B',
                weight: 5,
                opacity: 0.9,
            }).addTo(map);

            map.fitBounds(polyline.getBounds(), { padding: [40, 40] });

            L.circleMarker(coords[0], {
                radius: 9,
                color: '#E63A7B',
                weight: 3,
                fillColor: '#fff',
                fillOpacity: 1,
            }).addTo(map);
        });
        </script>
        @endpush
    @endif

</x-layouts::site>
