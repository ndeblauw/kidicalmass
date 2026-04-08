<x-layouts::site title="{{ $activity->title_nl }}">

@php($routeCoords = $activity->route_coordinates)
@php($hasMap = count($routeCoords) > 0)
@php($mainImage = $activity->getFirstMedia('main'))

    {{-- HERO — poster layout: dark blue bg, circular photo, angled title --}}
    <section class="activity-hero">

        {{-- Daisy: full bleed, right side, slightly cropped --}}
        <img src="{{ asset('img/logo-icon.png') }}"
             alt=""
             aria-hidden="true"
             class="activity-hero__daisy">

        {{-- Content aligned to container --}}
        <div class="container mx-auto px-4 activity-hero__inner">

            <div class="activity-hero__copy">
                <h1>{{ $activity->title_nl }}</h1>

                @if($activity->groups->isNotEmpty())
                    <div class="activity-hero__chapter">
                        <svg class="activity-hero__chapter-pin" viewBox="0 0 40 54" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M20 2C10.059 2 2 10.059 2 20C2 32 20 52 20 52C20 52 38 32 38 20C38 10.059 29.941 2 20 2Z" fill="var(--color-kidical-red)"/>
                            <circle cx="20" cy="20" r="7.5" fill="rgba(0,0,0,0.25)"/>
                            <circle cx="20" cy="20" r="4.5" fill="white"/>
                        </svg>
                        <div class="activity-hero__chapter-label">
                            @foreach($activity->groups as $group)
                                <span>{{ $group->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="activity-hero__visual">
                @if($mainImage)
                    <div class="activity-hero__photo">
                        <img src="{{ $mainImage->getUrl() }}"
                             alt="{{ $activity->title_nl }}"
                             class="activity-hero__img">
                    </div>
                @endif
                <img src="{{ asset('img/illustrations/kid-waving.png') }}"
                     alt=""
                     aria-hidden="true"
                     class="activity-hero__illustration">
            </div>

        </div>

    </section>

    {{-- META + MAP — full-bleed two-column --}}
    <section class="activity-info-map">

        {{-- LEFT: yellow meta panel --}}
        <div class="activity-info-map__meta">
            <div class="activity-info-map__meta-inner">
                <dl class="activity-info-list">
                    <div class="activity-info-item">
                        <div class="activity-info-item__icon-wrap">
                            <flux:icon.calendar-days variant="solid" class="activity-info-item__icon" aria-hidden="true" />
                        </div>
                        <div>
                            <dt>Wanneer</dt>
                            <dd>
                                <time datetime="{{ $activity->begin_date->toIso8601String() }}">
                                    {{ $activity->begin_date->translatedFormat('l j F') }}<br>
                                    {{ $activity->begin_date->translatedFormat('H\hi') }}
                                </time>
                            </dd>
                        </div>
                    </div>

                    <div class="activity-info-item">
                        <div class="activity-info-item__icon-wrap">
                            <flux:icon.map-pin variant="solid" class="activity-info-item__icon" aria-hidden="true" />
                        </div>
                        <div>
                            <dt>Vertrekpunt</dt>
                            <dd>{{ $activity->location }}</dd>
                        </div>
                    </div>

                    @if($activity->distance)
                        <div class="activity-info-item">
                            <div class="activity-info-item__icon-wrap">
                                <flux:icon.arrows-right-left variant="solid" class="activity-info-item__icon" aria-hidden="true" />
                            </div>
                            <div>
                                <dt>Afstand</dt>
                                <dd>{{ $activity->distance }}</dd>
                            </div>
                        </div>
                    @endif

                    @if($activity->duration)
                        <div class="activity-info-item">
                            <div class="activity-info-item__icon-wrap">
                                <flux:icon.clock variant="solid" class="activity-info-item__icon" aria-hidden="true" />
                            </div>
                            <div>
                                <dt>Duur</dt>
                                <dd>{{ $activity->duration }}</dd>
                            </div>
                        </div>
                    @endif

                    <div class="activity-info-item">
                        <div class="activity-info-item__icon-wrap">
                            <flux:icon.ticket variant="solid" class="activity-info-item__icon" aria-hidden="true" />
                        </div>
                        <div>
                            <dt>Deelname</dt>
                            <dd>Gratis &middot; Geen inschrijving nodig</dd>
                        </div>
                    </div>
                </dl>

                @if($activity->content_nl)
                    <div class="activity-info-description">
                        {!! nl2br(e($activity->content_nl)) !!}
                    </div>
                @endif

            </div>
        </div>

        {{-- RIGHT: map --}}
        @if($hasMap)
            <div class="activity-info-map__map">
                <div id="activity-map-hero"
                     class="activity-info-map__embed"
                     data-coordinates="{{ json_encode($routeCoords) }}">
                </div>
            </div>
        @endif

    </section>

    {{-- FIXED PROMISES — always shown on every Kidical Mass ride --}}
    <section class="activity-promises">
        <h2>Wat kun je verwachten?</h2>
        <div class="activity-promises__layout">
            <div class="activity-promises__illustration" aria-hidden="true">
                <img src="{{ asset('img/illustrations/person-with-boombox.png') }}" alt="">
            </div>
            <ul class="activity-promises__col" role="list">
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.clock variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>Op het tempo van het jongste kind</strong>
                    <p>We wachten op iedereen. Geen kind blijft achter.</p>
                </li>
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.shield-check variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>Roze hesjes voor en achter</strong>
                    <p>Vrijwilligers begeleiden de groep en zorgen dat iedereen veilig aankomt.</p>
                </li>
            </ul>
            <ul class="activity-promises__col" role="list">
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.musical-note variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>Muziek onderweg</strong>
                    <p>Een versierde bakfiets met geluid maakt van elke rit een feest.</p>
                </li>
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.flag variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>Korte verkeersbespreking aan de start</strong>
                    <p>Zodat iedereen weet wat te doen — ook als het je eerste keer is.</p>
                </li>
            </ul>
        </div>
    </section>

    {{-- MAP + CHAPTER/TEAM — two-column below the fold --}}
    @if($hasMap || $activity->groups->isNotEmpty())
        <section class="activity-lower">

            @if($hasMap)
                <div class="activity-map-col">
                    <h2>De route</h2>
                    <div class="activity-map-container">
                        <div id="activity-map"
                             class="activity-map-embed"
                             data-coordinates="{{ json_encode($routeCoords) }}"
                             data-komoot-url="{{ $activity->komoot_url ?? '' }}">
                        </div>
                        <div class="activity-map-info-strip">
                            <div class="activity-map-info-strip__stats">
                                @if($activity->distance)
                                    <span>{{ $activity->distance }}</span>
                                @endif
                                @if($activity->duration)
                                    <span>{{ $activity->duration }}</span>
                                @endif
                                <span class="activity-map-badge" id="activity-map-route-type" aria-live="polite"></span>
                            </div>
                            @if($activity->komoot_url)
                                <a href="{{ $activity->komoot_url }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="activity-map-komoot-link">
                                    Bekijk op Komoot
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </a>
                            @endif
                        </div>
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

    {{-- FIXED ACTION BAR --}}
    <div class="activity-actions-bar" x-data>
        <flux:button href="{{ route('activities.ical', $activity) }}" icon="calendar-days" variant="primary">
            + Agenda
        </flux:button>
        <div
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
                x-show="false"
                x-on:url-copied.window="$el.style.display='inline'; setTimeout(() => $el.style.display='none', 2000)"
                class="activity-hero__copied"
            >Gekopieerd!</span>
        </div>
    </div>

</x-layouts::site>
