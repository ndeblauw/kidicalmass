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
                            <dd>{!! nl2br(e($activity->location)) !!}</dd>
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

                    @if($activity->duration_label)
                        <div class="activity-info-item">
                            <div class="activity-info-item__icon-wrap">
                                <flux:icon.clock variant="solid" class="activity-info-item__icon" aria-hidden="true" />
                            </div>
                            <div>
                                <dt>Duur</dt>
                                <dd>{{ $activity->duration_label }}</dd>
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

        {{-- RIGHT: route map --}}
        @if($hasMap)
            <div class="activity-info-map__map">
                <div id="activity-map"
                     class="activity-info-map__route"
                     data-coordinates="{{ json_encode($routeCoords) }}">
                </div>
                <div class="activity-map-info-strip">
                    <div class="activity-map-info-strip__stats">
                        <span class="activity-map-stat">
                            <flux:icon.arrows-right-left class="activity-map-stat__icon" aria-hidden="true" />
                            {{ $activity->distance ?? '—' }}
                        </span>
                        <span class="activity-map-stat">
                            <flux:icon.clock class="activity-map-stat__icon" aria-hidden="true" />
                            {{ $activity->duration_label ?? '—' }}
                        </span>
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
        @endif

    </section>

    {{-- FIXED PROMISES — always shown on every Kidical Mass ride --}}
    <section class="activity-promises">
        <div class="activity-promises__layout">
            <div class="activity-promises__illustration">
                <h2>Wat kun je verwachten?</h2>
                <img src="{{ asset('img/illustrations/person-with-boombox.png') }}" alt="" aria-hidden="true" loading="lazy">
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

    {{-- ORGANIZERS + VOLUNTEER CTA --}}
    <section class="activity-organizers">
        <div class="activity-organizers__inner" x-data="{ open: false }">

            {{-- Normal content --}}
            <template x-if="!open"><div class="activity-organizers__content">

                <h2 class="activity-organizers__title">Van en voor de buurt</h2>

                @if($activity->groups->isNotEmpty())
                    <p class="activity-organizers__lead">
                        Georganiseerd door vrijwilligers van Kidical Mass
                        @foreach($activity->groups as $group)
                            <a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a>@if(!$loop->last), @endif
                        @endforeach
                    </p>
                @endif

                <div class="activity-organizers__team">
                    <div class="activity-organizers__person">
                        <img src="https://i.pravatar.cc/128?img=47"
                             alt="{{ Str::before($activity->author->name, ' ') }}"
                             class="activity-organizers__avatar"
                             loading="lazy">
                        <div class="activity-organizers__person-info">
                            <span class="activity-organizers__name">{{ Str::before($activity->author->name, ' ') }}</span>
                            <span class="activity-organizers__role">Organisator</span>
                        </div>
                    </div>
                </div>

                <div class="activity-organizers__volunteer">
                    <h3>Roze hesje worden?</h3>
                    <p>Als roze hesje begeleid je de groep en zorg je dat iedereen veilig aankomt. Je rijdt vooraan of achteraan, houdt kruispunten vrij en zorgt dat geen enkel kind achterblijft. <a href="{{ route('volunteer') }}" class="activity-organizers__volunteer-link">Lees hoe dat werkt.</a></p>
                    <div class="activity-organizers__actions">
                        <button x-on:click="open = true" class="activity-organizers__join-btn">
                            Ik wil meedoen als roze hesje
                        </button>
                    </div>
                </div>

            </div></template>

            {{-- Volunteer signup form (replaces content) --}}
            <template x-if="open">
                <div class="activity-organizers__content activity-organizers__signup">

                    <button x-on:click="open = false" class="activity-organizers__back-btn">
                        <flux:icon.arrow-left class="activity-organizers__back-icon" aria-hidden="true" />
                        Terug
                    </button>

                    <h2 class="activity-organizers__title">Meld je aan</h2>
                    <p class="activity-organizers__lead">We nemen zo snel mogelijk contact op met je als roze hesje.</p>

                    <livewire:volunteer-signup />

                </div>
            </template>

            <div class="activity-organizers__photo">
                <img src="{{ asset('img/pink-vest-volunteer.jpg') }}" alt="Roze hesjes begeleiden de groep" loading="lazy">
            </div>

        </div>
    </section>

    {{-- Support (PAT-10 contextual block) — the warm "just rode" moment --}}
    <x-support-callout variant="event" />

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

            const brandRed = getComputedStyle(document.documentElement).getPropertyValue('--color-kidical-red').trim() || '#E63A7B';

            const map = L.map(el, { zoomControl: true, scrollWheelZoom: false });

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 19,
            }).addTo(map);

            const polyline = L.polyline(coords, {
                color: brandRed,
                weight: 5,
                opacity: 0.95,
            }).addTo(map);

            map.invalidateSize();
            map.fitBounds(polyline.getBounds(), { padding: [8, 8], maxZoom: 16 });

            // Departure pin
            const departureIcon = L.divIcon({
                html: `<svg width="28" height="38" viewBox="0 0 28 38" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M14 1C6.82 1 1 6.82 1 14C1 24 14 37 14 37C14 37 27 24 27 14C27 6.82 21.18 1 14 1Z" fill="${brandRed}"/>
                    <circle cx="14" cy="14" r="5.5" fill="rgba(0,0,0,0.2)"/>
                    <circle cx="14" cy="14" r="3.5" fill="white"/>
                </svg><span class="activity-map-label">Vertrekpunt</span>`,
                className: 'activity-map-marker',
                iconAnchor: [14, 37],
                iconSize: [28, 38],
            });

            L.marker(coords[0], { icon: departureIcon }).addTo(map);
        });
        </script>
        @endpush
    @endif

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        const cards = document.querySelectorAll('.activity-promises__item');
        cards.forEach((card, i) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = `opacity 0.4s cubic-bezier(0.25, 1, 0.5, 1), transform 0.4s cubic-bezier(0.25, 1, 0.5, 1)`;
            card.style.transitionDelay = `${i * 90}ms`;
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        cards.forEach(card => observer.observe(card));
    });
    </script>
    @endpush

    {{-- FIXED ACTION BAR --}}
    <div class="activity-actions-bar" x-data="{ copied: false, shareTitle: @js($activity->title_nl) }">
        <flux:button href="{{ route('activities.ical', $activity) }}" icon="calendar-days" variant="ghost">
            Bewaar in agenda
        </flux:button>
        <flux:button
            icon="share"
            variant="ghost"
            x-on:click="
                if (navigator.share) {
                    navigator.share({ title: shareTitle, url: window.location.href })
                } else {
                    navigator.clipboard.writeText(window.location.href).then(() => {
                        copied = true
                        setTimeout(() => copied = false, 2000)
                    })
                }
            "
        >Deel</flux:button>
        <span x-show="copied" class="activity-actions-bar__copied">Gekopieerd!</span>
    </div>

</x-layouts::site>
