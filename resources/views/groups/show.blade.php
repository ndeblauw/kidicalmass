{{--
    Chapter page (P-11) — the local group's HOME. Intent-driven "v4" arc (Critique v4,
    Frederik 2026-06-23): the parade is the page's gravity. Order:
    1 hero (mission line only) -> 2 De volgende parade (split: left = next ride + subscribe
    CTA, right = real stat cards "sinds {jaar}" + "N ritten") -> 3 alle parades strip ->
    4 "Ook in {gemeente}" sky band of activity cards -> 5 photo gallery (kept) + colouring
    download -> 6 team carousel (relocated mid-page) -> 8 affiches + "met dank aan" (press
    REMOVED, lives on the channel Press page) -> 7 yellow CLOSING "help mee" band fused with
    the footer (on-demand signup reveal). Colour story blue -> white -> sky -> white -> yellow.
    NL, on the ride/show kit. Structure only here; appearance in resources/css/pages/chapters.css.
    Real now: rides/other-activities split (controller), real stat cards, on-demand reveal,
    group-specific J2 signup form. Faked (clearly commented): subscribe CTA, volunteer roles,
    affiches/sponsors, colouring download.
    Plan: docs/superpowers/plans/2026-06-23-chapter-page-v4.md ·
    Design: docs/wiki/design/30-skeleton/chapters.md (§ Critique v4).
--}}
<x-layouts::site title="{{ $group->name }}">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;

        // One typed agenda, grouped by day under a typographic date rail (<x-ride-day>),
        // exactly like the calendar's upcoming view. No featured hero — every activity
        // (rides, workshops, meetings) lists the same calm way.
        $agendaByDay = $activities->groupBy(fn ($a) => $a->begin_date->format('Y-m-d'));

        // Whether an actual ride is on the agenda — drives the "no ride yet" note, kept
        // honest even when only workshops/meetings are scheduled.
        $hasRide = $activities->contains(fn ($a) => $a->activity_type === \App\Enums\ActivityType::KIDICALMASS);

        $initialsOf = fn (string $name) => \Illuminate\Support\Str::of($name)->explode(' ')
            ->filter()->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('');

        // FAUX active volunteers + roles (no per-group volunteer roster/role field yet —
        // GitHub #37 / D-1). Shown only alongside a real lead, to preview "lead + crew".
        $fauxVolunteers = $group->users->isNotEmpty() ? [
            ['name' => 'Marieke', 'role' => 'roze hesje'],
            ['name' => 'Tariq', 'role' => 'roze hesje'],
            ['name' => 'Lien', 'role' => 'communicatie'],
        ] : [];

        $team = $group->users
            ->map(fn ($u) => ['name' => $u->name, 'role' => 'trekker', 'initials' => $initialsOf($u->name)])
            ->concat(collect($fauxVolunteers)->map(fn ($v) => ['name' => $v['name'], 'role' => $v['role'], 'initials' => $initialsOf($v['name'])]));

        // Brand-illustration placeholder per member (no portrait field yet — D-1).
        // Deterministic by name so a person keeps the same drawing across reloads.
        $teamIllustrations = [
            'waving-rider', 'relaxed-rider', 'cyclist-peace-sign', 'rider-with-flag',
            'volunteer-with-wrench', 'longtail-with-kid', 'cargo-bike-family',
        ];
        $illustrationFor = fn (string $name) => $teamIllustrations[crc32($name) % count($teamIllustrations)];

        // FAUX downloads — the flyers/posters a chapter offers (no per-group downloads
        // source yet; Nico wires the backend). Same frontend-placeholder pattern as
        // $fauxVolunteers above: design it now, swap the data source later. File type
        // rides in its own tag, not baked into the label.
        $fauxDownloads = [
            ['label' => 'Flyer '.$gemeente.' 2026', 'type' => 'PDF', 'url' => '#'],
            ['label' => 'Affiche om op te hangen', 'type' => 'PDF', 'url' => '#'],
            ['label' => 'Kleurplaat voor onderweg', 'type' => 'PDF', 'url' => '#'],
        ];

        $hasExtras = $partners->isNotEmpty() || $group->children->isNotEmpty();

        $allActivitiesUrl = route('activities.index', ['gemeente' => $group->id]);

        // Hero cover (section 1) — still the group's own identity photo, the cover of
        // its `gallery` collection. `php artisan dev:seed-group-gallery` populates it.
        $coverPhoto = $group->getMedia('gallery')->first();

        // "In beeld" (section 3b) follows the latest ride that has photos ($latestRide,
        // resolved in GroupController). The first photo becomes the "Laatste rit" poster
        // (eyebrow + title + a single "bekijk alle foto's" action over a scrim, the date
        // tear-off peeking from the corner); the rest fill the masonry wall as tiles (so
        // the poster photo isn't shown twice). The full set powers the lightbox, so the
        // tiles open at $loop->index + 1.
        $ridePhotos = $latestRide?->getMedia('gallery') ?? collect();
        $hasRideGallery = $latestRide !== null && $ridePhotos->isNotEmpty();
        $ridePhotoCount = $ridePhotos->count();
        $posterPhoto = $ridePhotos->first();
        $tilePhotos = $ridePhotos->slice(1)->take(6)->values();
        $rideRail = $latestRide ? \App\Support\RideDate::rail($latestRide->begin_date) : null;
    @endphp

    {{-- 1 · IDENTITY HERO — blue band on the shared .page-hero look (eyebrow = postcode,
         title = Kidical Mass / gemeente), via the --identity modifier. The group photo
         now lives INSIDE the hero, as a rounded card on the right beside the title
         (Frederik 2026-06-17) — the daisy peeks behind its top corner. --}}
    <header class="chapter-head chapter-head--identity">
        <img src="{{ asset('img/logos/logo-icon.png') }}" alt="" aria-hidden="true" class="chapter-head__daisy">
        <div class="container mx-auto px-4 chapter-head__inner">
            <div class="chapter-head__copy">
                <h1 class="page-hero__title">Kidical Mass<br>{{ $gemeente }}</h1>
                <p class="page-hero__lead">Wij fietsen samen met kinderen door {{ $gemeente }}, veilig, vrolijk, op kindertempo.</p>
            </div>

            <figure class="chapter-head__media">
                @if ($coverPhoto)
                    <img
                        src="{{ $coverPhoto->getUrl() }}"
                        alt="Foto van een Kidical Mass in {{ $gemeente }}"
                        class="chapter-head__photo"
                    >
                @else
                    <img
                        src="{{ asset('img/photography/ride-cinquantenaire-crowd.jpg') }}"
                        alt="Een grote groep gezinnen fietst samen door de straat tijdens een Kidical Mass in {{ $gemeente }}"
                        class="chapter-head__photo"
                    >
                @endif
            </figure>
        </div>
    </header>

    {{-- 2 · DE VOLGENDE PARADE — the soonest kidicalmass ride leads. Phase 1: plain card.
         Phase 2 turns this into the split-screen (left parade / right stat cards). --}}
    <section class="chapter-body chapter-parade">
        <h2 class="chapter-section__title">De volgende parade in {{ $gemeente }}</h2>
        <div class="chapter-parade__split">
            <div class="chapter-parade__main">
                @if ($upcomingRides->isNotEmpty())
                    @php $nextRide = $upcomingRides->first(); @endphp
                    <x-ride-day
                        :period-key="$nextRide->begin_date->format('Y-m-d')"
                        :commune="$gemeente"
                        :rows="[['item' => $nextRide]]" />
                    {{-- built-in subscribe CTA --}}
                    <x-newsletter-optin :group="$group" :show-join="false" class="chapter-parade__optin" />
                @else
                    <div class="chapter-next__card chapter-next__card--empty">
                        <p class="chapter-next__empty-lead">Nog geen fietstocht gepland.</p>
                        <p class="chapter-next__empty-body">We laten het je weten zodra {{ $gemeente }} vertrekt. Schrijf je hieronder in.</p>
                        <x-newsletter-optin :group="$group" :show-join="false" class="chapter-parade__optin" />
                    </div>
                @endif
            </div>
            <aside class="chapter-parade__proof">
                <div class="chapter-stat">
                    <span class="chapter-stat__num">sinds {{ $group->started_at?->format('Y') ?? '2023' }}</span>
                </div>
                @if ($pastRidesCount > 0)
                    <div class="chapter-stat">
                        <span class="chapter-stat__num">{{ $pastRidesCount }} {{ $pastRidesCount === 1 ? 'rit' : 'ritten' }}</span>
                        <span class="chapter-stat__label">samen gefietst</span>
                    </div>
                @endif
            </aside>
        </div>
    </section>

    {{-- 3 · ALLE PARADES — the remaining upcoming rides as a compact strip, paired under §2. --}}
    @if ($upcomingRides->count() > 1)
        <section class="chapter-body chapter-parades-strip">
            <h2 class="chapter-section__title">Alle parades</h2>
            <div class="chapter-agenda__list">
                @foreach ($upcomingRides->slice(1)->groupBy(fn ($a) => $a->begin_date->format('Y-m-d')) as $periodKey => $dayRides)
                    <x-ride-day :period-key="$periodKey" :commune="$gemeente" :rows="$dayRides->map(fn ($a) => ['item' => $a])->values()->all()" />
                @endforeach
            </div>
            <a href="{{ $allActivitiesUrl }}" class="link-plain chapter-parades-strip__all">Alle ritten (ook voorbije) →</a>
        </section>
    @endif

    {{-- 4 · OOK IN GEMEENTE — workshops, filmavonden, meetings. Phase 2: full-bleed sky
         band of activity cards. Each card shows type chip, title, date and location.
         Lighter presence than the §2 parade but with real visual weight. --}}
    @if ($otherActivities->isNotEmpty())
        <section class="chapter-body chapter-other chapter-other--sky">
            <h2 class="chapter-section__title">Ook in {{ $gemeente }}</h2>
            <ul class="chapter-other__grid" role="list">
                @foreach ($otherActivities as $activity)
                    <li class="chapter-other__card">
                        <span class="chapter-other__type">{{ $activity->activity_type->label() }}</span>
                        <h3 class="chapter-other__title">{{ $activity->title_nl }}</h3>
                        <time datetime="{{ $activity->begin_date->toDateString() }}">{{ $activity->begin_date->isoFormat('ddd D MMM') }}</time>
                        @if ($activity->location)<p class="chapter-other__loc">{{ $activity->location }}</p>@endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- 3b · IN BEELD — the latest ride's photos + an inline lightbox. Renders only when
         that ride has photos. The first cell is a date-rail lockup naming the ride (not a
         photo); then up to four photos, the rest reachable via the lightbox. Structure
         only; appearance in resources/css/pages/chapters.css. --}}
    @if ($hasRideGallery)
        <section
            class="chapter-body chapter-gallery"
            x-data="{
                photos: @js($ridePhotos->map(fn ($m) => ['url' => $m->getUrl(), 'name' => $m->name])->values()),
                isOpen: false,
                index: 0,
                trigger: null,
                touchX: null,
                entering: false,
                swap: false,
                slideDir: 1,
                fromX: '0px',
                fromY: '0px',
                accents: ['--color-kidical-yellow', '--color-kidical-red', '--color-kidical-blue', '--color-kidical-green', '--color-kidical-orange', '--color-kidical-sky'],
                reduced() { return window.matchMedia('(prefers-reduced-motion: reduce)').matches; },
                open(i, e) {
                    this.index = i;
                    this.trigger = e?.currentTarget ?? null;
                    const r = this.trigger?.getBoundingClientRect();
                    if (r && ! this.reduced()) {
                        this.fromX = (r.left + r.width / 2 - window.innerWidth / 2) + 'px';
                        this.fromY = (r.top + r.height / 2 - window.innerHeight / 2) + 'px';
                        this.entering = true;
                    }
                    this.isOpen = true;
                    this.$nextTick(() => requestAnimationFrame(() => {
                        this.entering = false;
                        this.$refs.closeBtn?.focus();
                    }));
                    this.preload();
                },
                close() {
                    this.isOpen = false;
                    this.$nextTick(() => this.trigger?.focus());
                },
                navigate(dir) {
                    this.slideDir = dir;
                    this.index = (this.index + dir + this.photos.length) % this.photos.length;
                    this.preload();
                    if (this.reduced()) { return; }
                    this.swap = true;
                    this.$nextTick(() => requestAnimationFrame(() => { this.swap = false; }));
                },
                next() { this.navigate(1); },
                prev() { this.navigate(-1); },
                preload() {
                    const n = this.photos.length;
                    [(this.index + 1) % n, (this.index - 1 + n) % n].forEach((j) => {
                        const img = new Image();
                        img.src = this.photos[j].url;
                    });
                },
                onTouchStart(e) { this.touchX = e.changedTouches[0].clientX; },
                onTouchEnd(e) {
                    if (this.touchX === null) { return; }
                    const dx = e.changedTouches[0].clientX - this.touchX;
                    if (Math.abs(dx) > 40) { dx < 0 ? this.next() : this.prev(); }
                    this.touchX = null;
                },
                trapTab(e) {
                    const els = [this.$refs.closeBtn, this.$refs.prevBtn, this.$refs.nextBtn].filter(Boolean);
                    if (! els.length) { return; }
                    e.preventDefault();
                    const dir = e.shiftKey ? -1 : 1;
                    const at = els.indexOf(document.activeElement);
                    els[(at + dir + els.length) % els.length].focus();
                },
            }"
            x-effect="document.documentElement.classList.toggle('is-lightbox-open', isOpen)"
            @keydown.escape.window="close()"
            @keydown.arrow-right.window="isOpen && next()"
            @keydown.arrow-left.window="isOpen && prev()"
        >
            {{-- Lead-in heading (Frederik 2026-06-19): the wall used to start cold, straight
                 off the opt-in band. A warm, inviting title gives the eye a beat and frames
                 the photos as "this could be you", not just an archive dump. --}}
            <h2 class="chapter-section__title">Zo ziet het eruit in {{ $gemeente }}</h2>

            <ul class="chapter-gallery__grid">
                {{-- First cell — a full-bleed photo poster of the latest ride. Its first
                     photo fills the tile and opens the lightbox; the calendar tear-off
                     (date it was) and a stacked eyebrow + "view all" action sit grounded
                     on a scrim at the bottom-left. The ride's own name is left off: on a
                     chapter page every ride is "the" ride, so the title only repeats the
                     page. A roze hesje of this chapter also gets a quiet "add photos" link. --}}
                <li class="chapter-gallery__cell chapter-gallery__cell--feature">
                    <div class="chapter-latest">
                        <button
                            type="button"
                            class="chapter-latest__media"
                            @click="open(0, $event)"
                            aria-label="Bekijk alle foto's van de laatste rit in {{ $gemeente }}"
                        >
                            <img src="{{ $posterPhoto->getUrl('card') }}" alt="" class="chapter-latest__bg">
                        </button>

                        <div class="chapter-latest__overlay">
                            <time
                                class="chapter-latest__rail"
                                datetime="{{ $latestRide->begin_date->toDateString() }}"
                            >
                                <span class="ride-day__bar" aria-hidden="true"></span>
                                <span class="ride-day__body">
                                    <span class="ride-day__day">{{ $latestRide->weekdayLabel }}</span>
                                    <span class="ride-day__date">{{ $rideRail['num'] }}</span>
                                    <span class="ride-day__month">{{ $rideRail['month'] }}</span>
                                </span>
                            </time>

                            <div class="chapter-latest__lockup">
                                <h3 class="chapter-latest__eyebrow">Recentste parade</h3>
                                <x-cta-button variant="blue" size="sm" x-on:click="open(0, $event)">{{ $ridePhotoCount > 1 ? "{$ridePhotoCount} foto's" : '1 foto' }}</x-cta-button>
                            </div>
                        </div>
                    </div>
                </li>

                @foreach ($tilePhotos as $media)
                    {{-- The 5th and 6th tiles only show once there's room for them — the
                         XL 4-column wall. Below that they'd overflow the calmer grid. --}}
                    <li @class(['chapter-gallery__cell', 'chapter-gallery__cell--xl' => $loop->index >= 4])>
                        <button
                            type="button"
                            class="chapter-gallery__tile"
                            @click="open({{ $loop->index + 1 }}, $event)"
                            aria-label="Bekijk foto {{ $loop->iteration + 1 }} groter"
                        >
                            <img
                                src="{{ $media->getUrl('card') }}"
                                alt="Foto van de laatste rit in {{ $gemeente }}"
                                loading="lazy"
                                class="chapter-gallery__img"
                            >
                        </button>
                    </li>
                @endforeach
            </ul>

            <div
                class="chapter-gallery__lightbox"
                x-show="isOpen"
                x-cloak
                :style="'--lb-accent: var(' + accents[index % accents.length] + ')'"
                @click.self="close()"
                @touchstart="onTouchStart($event)"
                @touchend="onTouchEnd($event)"
                @keydown.tab="trapTab($event)"
                role="dialog"
                aria-modal="true"
                :aria-label="'Foto ' + (index + 1) + ' van ' + photos.length"
            >
                <button type="button" class="chapter-gallery__lb-close" x-ref="closeBtn" @click="close()" aria-label="Sluiten">
                    <flux:icon.x-mark />
                </button>
                <button type="button" class="chapter-gallery__lb-nav chapter-gallery__lb-nav--prev" x-ref="prevBtn" @click="prev()" aria-label="Vorige foto">
                    <flux:icon.chevron-left />
                </button>
                <figure
                    class="chapter-gallery__lb-figure"
                    :style="entering ? `transform: translate(${fromX}, ${fromY}) scale(0.18); opacity: 0.35;` : ''"
                >
                    <img
                        :src="photos[index]?.url"
                        :alt="photos[index]?.name"
                        class="chapter-gallery__lb-img"
                        :style="swap ? `transform: translateX(calc(var(--lb-slide) * ${slideDir})); opacity: 0;` : ''"
                    >
                </figure>
                <button type="button" class="chapter-gallery__lb-nav chapter-gallery__lb-nav--next" x-ref="nextBtn" @click="next()" aria-label="Volgende foto">
                    <flux:icon.chevron-right />
                </button>
                <p class="chapter-gallery__lb-counter" aria-hidden="true" x-text="(index + 1) + ' / ' + photos.length"></p>
            </div>
        </section>
    @endif

    {{-- §5 KLEURPLAAT — a small illustrated download aside between the gallery and the
         team carousel. FAUX: real per-group download source pending Nico (#37).
         Remove this block (and the .chapter-colouring* CSS) once the backend is wired. --}}
    <aside class="chapter-body chapter-colouring">
        {{-- FAUX: preview points at an existing illustration SVG (no binary asset yet). --}}
        <img
            src="{{ asset('img/illustrations/caterpillar-bike.svg') }}"
            alt="Voorbeeld van de kleurplaat"
            class="chapter-colouring__preview"
        >
        <div class="chapter-colouring__content">
            <h3 class="chapter-colouring__title">Kleurplaat voor onderweg</h3>
            <p class="chapter-colouring__desc">Print hem uit, neem hem mee op de fiets en kleur onderweg!</p>
            {{-- FAUX: href="#" until Nico wires a real per-group download URL. --}}
            <x-cta-button variant="secondary" href="#">Download (PDF)</x-cta-button>
        </div>
    </aside>

    {{-- 6 · WIE ZIJN WIJ — the team carousel, relocated here (was in the closing band).
         Markup unchanged. Faces meet the newcomer BEFORE the recruitment ask in §7. --}}
    @if ($team->isNotEmpty())
        <section class="chapter-body chapter-team">
            <div class="chapter-team__carousel"
                x-data="{
                    start: true,
                    end: false,
                    scrollable: true,
                    page(dir) { const t = $refs.track; const card = t.querySelector('.chapter-team__card'); if (!card) return; const step = card.offsetWidth + parseFloat(getComputedStyle(t).columnGap || 0); t.scrollBy({ left: dir * step, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }); },
                    update() {
                        const t = $refs.track;
                        const max = t.scrollWidth - t.clientWidth;
                        const card = t.querySelector('.chapter-team__card');
                        const step = card ? card.offsetWidth + parseFloat(getComputedStyle(t).columnGap || 0) : 0;
                        this.scrollable = max > 1;
                        // half-a-card tolerance both ends: the full-bleed track's snap
                        // leaves a few-dozen px of lead-in scroll at rest, so an exact
                        // 0 / max test never fires
                        this.start = step === 0 || t.scrollLeft <= step / 2;
                        this.end = step > 0 && max - t.scrollLeft <= step / 2;
                    }
                }"
                x-init="$nextTick(() => update())"
                x-on:resize.window="update()">
                <div class="chapter-team__head">
                    <h2 class="chapter-team__headline">Wij zwaaien je welkom aan de start</h2>
                </div>

                <ul class="chapter-team__track" x-ref="track" role="region" aria-label="Team van {{ $gemeente }}" x-on:scroll.passive="update()">
                    @foreach ($team as $member)
                        <li class="chapter-team__card">
                            <span class="chapter-team__photo">
                                <img src="{{ asset('img/illustrations/'.$illustrationFor($member['name']).'.svg') }}" alt="" aria-hidden="true">
                            </span>
                            <span class="chapter-team__name">{{ explode(' ', trim($member['name']))[0] }}</span>
                            <span class="chapter-team__role">{{ $member['role'] }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="chapter-team__nav" x-show="scrollable" x-cloak>
                    <button type="button" class="chapter-team__btn" aria-label="Vorige teamleden" x-on:click="page(-1)" :disabled="start">
                        <flux:icon.chevron-left aria-hidden="true" />
                    </button>
                    <button type="button" class="chapter-team__btn" aria-label="Volgende teamleden" x-on:click="page(1)" :disabled="end">
                        <flux:icon.chevron-right aria-hidden="true" />
                    </button>
                </div>
            </div>
        </section>
    @endif

    {{-- 8 · AFFICHES + MET DANK AAN — quiet white tail. Real partners (visible, group-scoped)
         and faux downloads. Press moved to the channel-wide Press page. D-11 closed. --}}
    @if ($hasExtras)
        <section class="chapter-body chapter-body--tail">
            @if ($partners->isNotEmpty())
                <div class="chapter-extras">
                    {{-- One kind of friend, always a text link (never a logo: keeps
                         volunteer-uploaded artwork out). A plain wrapping run that
                         stays compact at any length, with no cap. --}}
                    <div class="chapter-extras__block">
                        <h3 class="chapter-section__title">Met dank aan</h3>
                        <ul class="chapter-partners" role="list">
                            @foreach ($partners as $partner)
                                <li class="chapter-partners__item">
                                    @if ($partner->url)
                                        <a href="{{ $partner->url }}" target="_blank" rel="noopener noreferrer" class="chapter-partners__link">{{ $partner->name }}</a>
                                    @else
                                        <span class="chapter-partners__name">{{ $partner->name }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Downloads — chapter flyers/posters. FAUX placeholder for now (Nico
                         wires the source). Sits alongside partners, so empty chapters stay
                         empty. Compact: icon + label + a small type tag. --}}
                    @if (! empty($fauxDownloads))
                        <div class="chapter-extras__block">
                            <h3 class="chapter-section__title">Downloads</h3>
                            <ul class="chapter-downloads" role="list">
                                @foreach ($fauxDownloads as $download)
                                    <li class="chapter-downloads__item">
                                        <a href="{{ $download['url'] }}" class="chapter-downloads__link">
                                            <svg class="chapter-downloads__icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                            <span class="chapter-downloads__label">{{ $download['label'] }}</span>
                                            <span class="chapter-downloads__type">{{ $download['type'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Parent/region node: minimal children list so a parent page does not break. --}}
            @if ($group->children->isNotEmpty())
                <div class="chapter-children">
                    <h3 class="chapter-section__title">Lokale groepen in {{ $group->name }}</h3>
                    <ul class="flex flex-wrap gap-2.5">
                        @foreach ($group->children as $child)
                            <li><a href="{{ route('groups.show', $child) }}" class="grp-pill link-plain">{{ $child->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    @endif

    {{-- 7 · YELLOW CLOSING BAND — §7 help mee: the "join us" recruitment climax, fused
         with the footer. The team carousel has moved to §6 (above). The seam illo and
         "help mee" form remain here. Rendered in the layout's `closing` slot so it sits
         flush above the yellow footer (main drops to pb-0). --}}
    <x-slot:closing>
        <section class="chapter-team-band" id="aanmelden">
            <div class="container mx-auto px-4 chapter-team-band__inner">
                {{-- Pink-vest volunteer, straddling the seam between the white extras and
                     this yellow band (Frederik 2026-06-18): anchored bottom-right of the
                     extras, it rises out of the white zone and dips ~4rem into the yellow,
                     a playful hand-off between the two colour zones. Lives here (not in the
                     white section) so it paints above the yellow band it overlaps. Decorative;
                     hidden on narrow screens where there's no empty corner to grow into. --}}
                @if ($hasExtras)
                    <img src="{{ asset('img/illustrations/volunteer-with-wrench.svg') }}" alt="" aria-hidden="true" class="chapter-seam-illo">
                @endif

                {{-- HELP MEE — recruitment CTA. On-demand reveal: the band stays light,
                     the helper expands the group-specific form right under the button.
                     Auto-open via ?intent=volunteer. --}}
                <div class="chapter-join scroll-mt-24" x-data="{ open: {{ request('intent') === 'volunteer' ? 'true' : 'false' }} }" @open-volunteer.window="open = true">
                    <div class="chapter-join__cta"
                         x-show="!open"
                         x-transition:leave="chapter-join__cta--leave"
                         x-transition:leave-start="chapter-join__cta--leave-start"
                         x-transition:leave-end="chapter-join__cta--leave-end">
                        <h2>Help mee in {{ $gemeente }}</h2>
                        <p class="chapter-join__tagline">Een paar uur per maand, je hoeft geen fietsexpert te zijn.</p>
                        <div class="chapter-join__actions">
                            <x-cta-button variant="blue" icon="heart" href="#aanmelden" x-on:click.prevent="open = true">Ja, ik wil meehelpen</x-cta-button>
                            <x-cta-button variant="secondary" href="{{ route('volunteer') }}">Meer over meehelpen</x-cta-button>
                        </div>
                    </div>

                    <div class="chapter-join__panel" x-show="open" x-cloak>
                        <div class="chapter-join__aside">
                            <h2 class="chapter-join__welcome">Fijn dat je wil meehelpen in {{ $gemeente }}!</h2>
                            <p class="chapter-join__welcome-sub">Je hoeft niets speciaals te kunnen, goesting volstaat. Laat je gegevens achter, dan nemen we snel contact met je op.</p>
                        </div>
                        <div class="chapter-join__form-col">
                            <livewire:chapter-volunteer-signup :group="$group" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </x-slot:closing>
</x-layouts::site>
