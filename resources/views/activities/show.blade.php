<x-layouts::site title="{{ $activity->title_nl }}" :nav-chapter="$activity->groups->first()">

@php
    $routeCoords = $activity->route_coordinates;
    $hasMap = count($routeCoords) > 0;
    $mainImage = $activity->getFirstMedia('main');

    // The organising group's public members, deduped across multiple groups. Members
    // who opted out (is_public = false) are excluded. No per-ride volunteer roster
    // exists yet (GitHub #37 / D-1); avatars are the deterministic brand illustrations,
    // keyed by name so each person keeps the same one.
    $volunteers = $activity->groups->flatMap->publicMembers->unique('id')->values();

    $teamIllustrations = [
        'waving-rider', 'relaxed-rider', 'rider-with-flag',
        'volunteer-with-wrench', 'longtail-with-kid', 'cargo-bike-family',
    ];
    $illustrationFor = fn (string $name) => $teamIllustrations[crc32($name) % count($teamIllustrations)];

    // "Wat kan je verwachten" collage (upcoming rides). Same two snapshots the home
    // "Nieuw hier?" block leads with; placement mirrors that collage's first beat.
    $expectPhotos = [
        ['src' => 'img/photography/ride-girl-pink-jacket-crossing.webp', 'alt' => 'Kinderen fietsen samen over een kruispunt tijdens een rit.', 'x' => '38%', 'y' => '34%', 'w' => '56%', 'r' => '-5deg', 'pos' => 'center 40%'],
        ['src' => 'img/photography/cargo-bike-kidical-mass-brussels.webp', 'alt' => 'Bakfiets met het Kidical Mass-logo en zwaaiende kinderen onderweg.', 'x' => '70%', 'y' => '64%', 'w' => '50%', 'r' => '6deg', 'pos' => 'center 45%'],
    ];

    // "Dankzij buren zoals jij" collage — the self-organising crew in action:
    // wegkapiteins in pink vests + the team celebrating a finished ride. Mirror of
    // the "Wat kan je verwachten?" collage; here the photos lead on the left.
    $teamPhotos = [
        ['src' => 'img/photography/ride-trio-pink-vest-lei-portrait.webp', 'alt' => 'Drie vrijwilligers in roze hesje, klaar om een rit te begeleiden.', 'x' => '38%', 'y' => '34%', 'w' => '56%', 'r' => '-5deg', 'pos' => 'center 35%'],
        ['src' => 'img/photography/team-blue-sweatshirts-celebration.webp', 'alt' => 'Het organiserende team viert samen na een geslaagde rit.', 'x' => '70%', 'y' => '64%', 'w' => '50%', 'r' => '6deg', 'pos' => 'center 45%'],
    ];

    // Social-proof credit line: first two first names, then "en N anderen".
    $volunteerNames = $volunteers->map(fn ($person) => \Illuminate\Support\Str::before(trim($person->name), ' '))->values();
    $volunteerCount = $volunteerNames->count();
    $volunteerCredit = match (true) {
        $volunteerCount >= 3 => "{$volunteerNames[0]}, {$volunteerNames[1]} en ".($volunteerCount - 2).' anderen',
        $volunteerCount === 2 => "{$volunteerNames[0]} en {$volunteerNames[1]}",
        default => (string) ($volunteerNames[0] ?? ''),
    };
    $volunteerVerb = $volunteerCount === 1 ? 'maakt' : 'maken';
@endphp
@php($state = $activity->lifecycleState())
@php($isPast = $state->isPastState())
@php($primaryGroup = $activity->groups->first())

    {{-- HERO — blue band, tilted photo card dipping into the white below (new look) --}}
    <header class="activity-head">
        <div class="container mx-auto px-4 activity-head__inner">

            <div class="activity-head__copy">
                @if($isPast)
                    <p class="activity-head__past">Voorbij</p>
                @endif

                {{-- Date tear-off sits beside the title as the hero's date anchor. No
                     date·time eyebrow: the tile carries the day, and the full date + time
                     live in Praktisch (Startuur). The organising group is no longer
                     repeated here either (its zip rides beside the nav logo, and it's
                     credited in "Van en voor de buurt" below). The hero gives the tile a
                     friendlier face (white number on a solid red header, soft shadow,
                     no weekday line) via the .activity-head__date scope in activity.css. --}}
                <div class="activity-head__headline">
                    <x-ride-date-tile
                        :date="$activity->begin_date"
                        accent="var(--color-kidical-red)"
                        :rotation="-3"
                        size="lg"
                        class="activity-head__date" />
                    <h1 class="page-hero__title">{{ $activity->title_nl }}</h1>
                </div>

                @if($activity->content_nl)
                    <x-intro-text class="activity-head__lead">{!! nl2br(e($activity->content_nl)) !!}</x-intro-text>
                @endif
            </div>

            <figure class="activity-head__media">
                @if($mainImage)
                    <img src="{{ $mainImage->getUrl() }}" alt="{{ $activity->title_nl }}" class="activity-head__photo">
                @else
                    <img src="{{ asset('img/photography/ride-cinquantenaire-crowd.jpg') }}" alt="" aria-hidden="true" class="activity-head__photo">
                @endif
            </figure>

        </div>

        {{-- THE PARADE ROLLS IN — brand riders along the blue→white seam, so the
             recognizable illustration style greets you up top (it mirrors the loved
             closing band below). Purely decorative: aria-hidden, and the riders bob
             in on load only when motion is welcome. --}}
        <div class="container mx-auto px-4 activity-head__parade" aria-hidden="true">
            <img src="{{ asset('img/illustrations/rider-with-flag.svg') }}" alt="" class="activity-head__rider">
            <img src="{{ asset('img/illustrations/relaxed-rider.svg') }}" alt="" class="activity-head__rider activity-head__rider--flip">
            <img src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" class="activity-head__rider">
        </div>
    </header>

    {{-- BODY PANEL — opaque white sheet that rides up over the sticky hero and covers the
         riders' wheels (the /events scroll-over pattern). Full-bleed panel, content
         re-centred by the inner container. --}}
    <div class="activity-stack">
        <div class="activity-stack__inner container mx-auto px-4">

        {{-- PHOTO BLOCK — recap leads with the gallery; just-past shows the nudge; upcoming has none --}}
        @if($activity->isRecap())
            <x-ride-gallery
                :photos="$activity->getMedia('gallery')"
                title="In beeld"
                :date="$activity->begin_date"
                :commune="$primaryGroup?->name" />
        @elseif($activity->isAwaitingPhotos())
            <x-ride-photo-nudge :activity="$activity" />
        @endif

        {{-- PRAKTISCH — facts + route, paired with a "stay in the loop" card --}}
        <section class="activity-praktisch">
            @php($departure = \Illuminate\Support\Str::of($activity->location)->replace("\n", ', ')->trim())
            <article class="activity-facts">
                <div class="activity-facts__body">
                    <div class="activity-facts__main">
                        <dl class="activity-facts__meta">
                            <div class="activity-facts__meta-item">
                                <x-icon-chip color="red" size="sm" aria-hidden="true">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4.5" width="18" height="16.5" rx="2"/><path d="M3 9.5h18M8 2.5v4M16 2.5v4"/></svg>
                                </x-icon-chip>
                                <div>
                                    <dt>Wanneer</dt>
                                    <dd><time datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ \Illuminate\Support\Str::ucfirst($activity->dateFull) }}, {{ $activity->timeLabel }}</time></dd>
                                </div>
                            </div>

                            @if($activity->distance)
                                <div class="activity-facts__meta-item">
                                    <x-icon-chip color="red" size="sm" aria-hidden="true">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7L4 11l4 4"/><path d="M4 11h16"/><path d="M16 17l4-4-4-4"/></svg>
                                    </x-icon-chip>
                                    <div>
                                        <dt>Afstand</dt>
                                        <dd>{{ $activity->distance }}</dd>
                                    </div>
                                </div>
                            @endif

                            @if($activity->duration_label)
                                <div class="activity-facts__meta-item">
                                    <x-icon-chip color="red" size="sm" aria-hidden="true">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                    </x-icon-chip>
                                    <div>
                                        <dt>Duur</dt>
                                        <dd>{{ $activity->duration_label }}</dd>
                                    </div>
                                </div>
                            @endif

                            <div class="activity-facts__meta-item">
                                <x-icon-chip color="red" size="sm" aria-hidden="true">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2 2 2 0 0 0 0 4 2 2 0 0 1-2 2H6a2 2 0 0 1-2-2 2 2 0 0 0 0-4z"/></svg>
                                </x-icon-chip>
                                <div>
                                    <dt>Deelname</dt>
                                    <dd>Gratis &middot; geen inschrijving nodig</dd>
                                </div>
                            </div>
                        </dl>
                    </div>

                    {{-- Route — the real GPX track when present, else the stylised brand
                         fallback (same dual logic as the chapter page's <x-next-ride>).
                         On the real map the departure point is a popup pinned to the start
                         marker (built by <x-route-map>); the <dl> below is the accessible,
                         no-JS fallback. The faux map has no pin, so its <dl> is the visible
                         corner chip. The Komoot link anchors into the map's bottom corner. --}}
                    <div class="activity-facts__map">
                        @if($hasMap)
                            <x-route-map :coordinates="$routeCoords" :interactive="false" label="{{ $departure }}" eyebrow="Vertrekpunt" class="activity-facts__route" aria-hidden="true" />
                            <dl class="activity-facts__map-label activity-facts__map-label--fallback">
                                <dt>Vertrekpunt</dt>
                                <dd>{{ $departure }}</dd>
                            </dl>
                        @else
                            <div class="activity-facts__route-faux" aria-hidden="true">
                                <svg viewBox="0 0 440 320" preserveAspectRatio="xMidYMid slice" class="activity-facts__route-svg">
                                    <path class="activity-facts__route-line" d="M50 270 C 120 260, 150 210, 200 200 S 300 180, 330 120 400 75 405 45" fill="none"/>
                                    <circle class="activity-facts__route-dot" cx="200" cy="200" r="5"/>
                                    <circle class="activity-facts__route-dot" cx="330" cy="120" r="5"/>
                                    <circle class="activity-facts__route-start" cx="50" cy="270" r="10"/>
                                </svg>
                            </div>
                            <dl class="activity-facts__map-label">
                                <dt>Vertrekpunt</dt>
                                <dd>{{ $departure }}</dd>
                            </dl>
                        @endif

                        @if($activity->komoot_url)
                            <x-cta-button
                                :href="$activity->komoot_url"
                                variant="secondary"
                                size="sm"
                                icon="arrow"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="activity-facts__map-komoot"
                            >Bekijk op Komoot</x-cta-button>
                        @endif
                    </div>
                </div>
            </article>

            {{-- DEEL — a quiet, in-context share ask right beside the practical details. --}}
            <aside class="activity-share">
                <div class="activity-share__text">
                    @if($isPast)
                        <h2>Deel de herinnering</h2>
                        <p class="activity-share__body">Laat anderen zien hoe fijn het was.</p>
                    @else
                        <h2>Vrienden mee?</h2>
                        <p class="activity-share__body">Nodig anderen uit. Want samen fietsen is leuker.</p>
                    @endif
                </div>

                <x-share-links
                    :url="route('activities.show', $activity)"
                    :title="$activity->title_nl"
                    :date="$activity->begin_date->translatedFormat('l j F')" />
            </aside>
        </section>

        {{-- WAT KAN JE VERWACHTEN — newcomer reassurance (mirrors the home "Nieuw hier?"
             block: warm copy + a how-it-works CTA beside a lively photo collage),
             only for upcoming rides --}}
        @unless($isPast)
        <section class="activity-expect">
            <div class="activity-expect__copy">
                <h2 class="text-kidical-ink">Wat kan je verwachten?</h2>
                <p>Nog nooit meegefietst? Geen zorgen. Een Kidical Mass is een rustige, vrolijke fietsparade door je eigen buurt, op kindertempo, met de kruispunten veilig vrijgehouden. Je hoeft niets te kunnen en je hoeft je niet in te schrijven. Gewoon komen en meefietsen.</p>
                <p><x-cta-button :href="route('getting-started')" variant="secondary" disc="blue">Zo werkt een rit</x-cta-button></p>
            </div>

            <x-photo-collage
                class="activity-expect__collage"
                :photos="$expectPhotos" />
        </section>
        @endunless

        {{-- DANKZIJ BUREN ZOALS JIJ — the self-organising crew behind the ride: warm
             copy + a social-proof face-stack beside a lively photo collage. A mirror
             of "Wat kan je verwachten?" — here the photos lead on the left. --}}
        @if($activity->groups->isNotEmpty())
        <section class="activity-team">
            <x-photo-collage
                class="activity-team__collage"
                :photos="$teamPhotos" />

            <div class="activity-team__copy">
                <h2 class="text-kidical-ink">Dankzij buren zoals jij.</h2>
                <p class="activity-team__lead">Zij plannen de ritten zelf en houden onderweg de kruispunten vrij, zodat iedereen veilig kan meefietsen.</p>

                @if($volunteers->isNotEmpty())
                    <div class="activity-team__proof">
                        <ul class="activity-team__stack" role="list">
                            @foreach($volunteers->take(5) as $person)
                                <li class="activity-team__face">
                                    <img src="{{ asset('img/illustrations/'.$illustrationFor($person->name).'.svg') }}" alt="" aria-hidden="true">
                                </li>
                            @endforeach
                            @if($volunteers->count() > 5)
                                <li class="activity-team__more">+{{ $volunteers->count() - 5 }}</li>
                            @endif
                        </ul>
                        <p class="activity-team__names"><strong>{{ $volunteerCredit }}</strong> {{ $volunteerVerb }} deze ritten mogelijk.</p>
                    </div>
                @endif

                @if($primaryGroup)
                    <x-cta-button :href="route('groups.show', $primaryGroup)" variant="secondary" disc="blue">Leer {{ $primaryGroup->name }} kennen</x-cta-button>
                @endif
            </div>
        </section>
        @endif

        {{-- STEUN — contextual support ask, quiet & contained. Only on past rides: the
             "steun de volgende rit" ask lands once someone has just ridden, not before. --}}
        @if($isPast)
            <x-support-callout variant="event" :contained="true" />
        @endif

        {{-- The share ask now lives in the Praktisch sidebar (activity-share), so the old
             full-width share band is intentionally gone — no second, duplicate share row. --}}

        </div>
    </div>

    <x-slot:closing>
        @if($isPast && $primaryGroup)
            <x-closing-cta
                heading="Meer ritten van Kidical Mass {{ $primaryGroup->name }}?"
                :href="route('groups.show', $primaryGroup)"
                label="Ontdek de groep" />
        @else
            {{-- Upcoming: the "how it works" ask already lives above (Zo werkt een rit,
                 beside the map's promises), so the closing band points elsewhere: stay
                 in the loop for the next rides via the newsletter. --}}
            <x-closing-cta
                heading="{{ $primaryGroup ? 'Mis geen rit van Kidical Mass '.$primaryGroup->name : 'Geen rit missen?' }}"
                :href="route('newsletter.show', ['locale' => app()->getLocale()])"
                label="Schrijf je in voor updates" />
        @endif
    </x-slot:closing>

</x-layouts::site>
