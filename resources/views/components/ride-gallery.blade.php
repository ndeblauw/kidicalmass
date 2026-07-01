{{--
    Reusable ride photo gallery component with inline Alpine lightbox.
    Used by:
      - Chapter page (groups/show.blade.php) — shows the latest ride's gallery.
      - Recap state (activities/show.blade.php) — shows the ride's own gallery.

    Props:
      - photos      — Spatie MediaCollection (gallery), required, non-empty.
      - title       — Feature-cell overlay heading (default: 'In beeld').
      - date        — Carbon instance; the component builds the date tear-off via RideDate::rail().
      - commune     — Woven into photo alt text; pass the group's commune/name, or null.
      - href        — When set, renders a "Bekijk de hele rit" link beneath the grid.
      - card        — Optional named slot; extra in-grid cell (chapter passes its opt-in here).
--}}
@props([
    'photos',
    'title' => 'In beeld',
    'date' => null,
    'commune' => null,
    'href' => null,
])

@php
    $coverPhoto = $photos->first();
    $tilePhotos = $photos->slice(1)->values();
    // Keep the chapter page's EXACT ragged-row cap: 9 or 5
    $tilePhotos = $tilePhotos->take($tilePhotos->count() >= 9 ? 9 : 5);
    // Photos not shown as the cover or a tile — surfaced as a "+N, all photos" overlay on the
    // last tile so people know the wall is a doorway to the full set (the lightbox cycles all).
    $hiddenCount = max(0, $photos->count() - 1 - $tilePhotos->count());
    $rideRail = $date ? \App\Support\RideDate::rail($date) : null;
@endphp

<div
    x-data="{
        photos: @js($photos->map(fn ($m) => ['url' => $m->getUrl(), 'name' => $m->name])->values()),
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
    <ul class="ride-gallery__grid">
        {{-- First cell — a full-bleed photo poster. The cover photo fills the tile and opens
             the lightbox; the title leads as a heading top-right, with the calendar tear-off
             (date it was) tucked just beneath it. --}}
        <li class="ride-gallery__cell ride-gallery__cell--feature">
            <div class="ride-gallery__feature">
                <button
                    type="button"
                    class="ride-gallery__feature-media"
                    @click="open(0, $event)"
                    aria-label="Bekijk alle foto's{{ $commune ? ' in ' . $commune : '' }}"
                >
                    <img src="{{ $coverPhoto->getUrl('card') }}" alt="" class="ride-gallery__feature-bg" loading="lazy" decoding="async">
                </button>

                {{-- Title + the shared calendar tear-off (same lockup as the agenda),
                     red accent, clustered top-right over the photo scrim. --}}
                <div class="ride-gallery__feature-head">
                    <p class="ride-gallery__feature-title">{{ $title }}</p>

                    @if ($rideRail)
                        <div
                            class="ride-gallery__feature-cal ride-day__cal"
                            style="--ride-day-rot: {{ $rideRail['rotation'] }}deg; --ride-accent: var(--color-kidical-red);"
                        >
                            <time class="ride-day__rail" datetime="{{ $date->toDateString() }}">
                                <span class="ride-day__bar" aria-hidden="true"></span>
                                <span class="ride-day__body">
                                    <span class="ride-day__day">{{ $date->isoFormat('dddd') }}</span>
                                    <span class="ride-day__date">{{ $rideRail['num'] }}</span>
                                    <span class="ride-day__month">{{ $rideRail['month'] }}</span>
                                </span>
                            </time>
                        </div>
                    @endif
                </div>
            </div>
        </li>

        {{-- Optional in-grid card slot — callers inject their own content (e.g. the chapter's
             newsletter opt-in), or omit it (recap page). --}}
        {{ $card ?? '' }}

        @foreach ($tilePhotos as $media)
            {{-- Tiles past the fourth only show once there's room for them — the XL 4-column
                 wall fits eight in three rows; below that, only the first four show. --}}
            @php($isMoreTile = $loop->last && $hiddenCount > 0)
            <li @class(['ride-gallery__cell', 'ride-gallery__cell--xl' => $loop->index >= 4]) @if ($loop->index >= 4) data-gallery-xl @endif>
                <button
                    type="button"
                    class="ride-gallery__tile"
                    data-gallery-tile
                    @click="open({{ $loop->index + 1 }}, $event)"
                    aria-label="{{ $isMoreTile ? "Bekijk alle foto's" : 'Bekijk foto ' . ($loop->iteration + 1) . ' groter' }}"
                >
                    <img
                        src="{{ $media->getUrl('card') }}"
                        alt=""
                        loading="lazy"
                        class="ride-gallery__img"
                    >
                    @if ($isMoreTile)
                        <span class="ride-gallery__more" aria-hidden="true">
                            <span class="ride-gallery__more-count">+{{ $hiddenCount }}</span>
                            <span class="ride-gallery__more-label">Bekijk alle foto's</span>
                        </span>
                    @endif
                </button>
            </li>
        @endforeach
    </ul>

    @if($href)
        <a href="{{ $href }}" class="ride-gallery__link">
            Bekijk de hele rit
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
    @endif

    <div
        class="ride-gallery__lightbox"
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
        <button type="button" class="ride-gallery__lb-close" x-ref="closeBtn" @click="close()" aria-label="Sluiten">
            <flux:icon.x-mark />
        </button>
        <button type="button" class="ride-gallery__lb-nav ride-gallery__lb-nav--prev" x-ref="prevBtn" @click="prev()" aria-label="Vorige foto">
            <flux:icon.chevron-left />
        </button>
        <figure
            class="ride-gallery__lb-figure"
            :style="entering ? `transform: translate(${fromX}, ${fromY}) scale(0.18); opacity: 0.35;` : ''"
        >
            <img
                :src="photos[index]?.url"
                :alt="photos[index]?.name"
                class="ride-gallery__lb-img"
                :style="swap ? `transform: translateX(calc(var(--lb-slide) * ${slideDir})); opacity: 0;` : ''"
            >
        </figure>
        <button type="button" class="ride-gallery__lb-nav ride-gallery__lb-nav--next" x-ref="nextBtn" @click="next()" aria-label="Volgende foto">
            <flux:icon.chevron-right />
        </button>
        <p class="ride-gallery__lb-counter" aria-hidden="true" x-text="(index + 1) + ' / ' + photos.length"></p>
    </div>
</div>
