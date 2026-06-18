{{--
    Chapter page (P-11) — the local group's HOME. Family-first arc, re-arranged onto the
    shared band rhythm (Critique + normalize, Frederik 2026-06-09): blue identity hero ->
    group photo (flush) -> ONE white agenda (typed; every activity day-grouped on the shared
    ride kit, full container width) -> quiet white extras (vrienden + downloads) -> ONE
    yellow CLOSING band that fuses with the footer: first WHO runs the group (faces), then
    the "help mee" recruitment CTA whose signup form opens on demand right under the button.
    Colour story blue -> white -> yellow, like the sibling pages. The yellow band lives in
    the layout's `closing` slot so it sits flush against the yellow footer (main -> pb-0).
    NL, on the ride/show kit. Structure only here; appearance in resources/css/pages/chapters.css.
    Real now: typed agenda, partners (group-scoped, section 4), press articles (section 4),
    on-demand reveal, group-specific J2 signup form.
    Plan: docs/wiki/design/30-skeleton/chapters.md (§ Chapter Page + Critique v3).
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

        $hasExtras = $partners->isNotEmpty() || $pressArticles->isNotEmpty() || $group->children->isNotEmpty();

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
                @if (filled($group->zip))
                    <p class="page-hero__eyebrow">{{ $group->zip }}</p>
                @endif
                <h1 class="page-hero__title">Kidical Mass<br>{{ $gemeente }}</h1>
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

    {{-- 3 · OP DE AGENDA — white. A tall heart/30 sign anchors a left column beside the
         agenda on wider screens; the column collapses away on mobile (illustration hidden). --}}
    <section class="chapter-body chapter-agenda">
        <div class="chapter-agenda__grid">
            <aside class="chapter-agenda__aside" aria-hidden="true">
                <img src="{{ asset('img/illustrations/heart-30-sign.svg') }}" alt="" class="chapter-agenda__sign">
            </aside>

            <div class="chapter-agenda__main">
                <h2 class="chapter-section__title">Op de agenda in {{ $gemeente }}</h2>

                {{-- No ride on the agenda yet (there may still be workshops/meetings below):
                     a warm, honest note — never a workshop dressed as a ride. The opt-in below
                     handles "leave your email". --}}
                @unless ($hasRide)
                    <div class="chapter-next__card chapter-next__card--empty">
                        <p class="chapter-next__empty-lead">Nog geen fietstocht gepland.</p>
                        <p class="chapter-next__empty-body">We laten het je weten zodra {{ $gemeente }} vertrekt. Schrijf je hieronder in.</p>
                    </div>
                @endunless

                {{-- Rides, workshops and meetings, grouped by day with the date-rail lockup,
                     exactly like the calendar's upcoming agenda. --}}
                @if ($activities->isNotEmpty())
                    <div class="chapter-agenda__list">
                        @foreach ($agendaByDay as $periodKey => $dayActivities)
                            <x-ride-day :period-key="$periodKey" :rows="$dayActivities->map(fn ($a) => ['item' => $a])->values()->all()" />
                        @endforeach
                    </div>
                @endif

                {{-- Opt-in normally lives as the last tile of the gallery below; when there is
                     no gallery (≤1 photo) it falls back to here, right under the rides in the
                     same column, so the "schrijf je hieronder in" note above keeps its promise. --}}
                @unless ($hasRideGallery)
                    <div class="mt-12">
                        <x-newsletter-optin :group="$group" :show-join="true" />
                    </div>
                @endunless
            </div>
        </div>
    </section>

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
                open(i) { this.index = i; this.isOpen = true; this.$nextTick(() => this.$refs.closeBtn?.focus()); },
                close() { this.isOpen = false; },
                next() { this.index = (this.index + 1) % this.photos.length; },
                prev() { this.index = (this.index - 1 + this.photos.length) % this.photos.length; },
            }"
            x-effect="document.documentElement.classList.toggle('is-lightbox-open', isOpen)"
            @keydown.escape.window="close()"
            @keydown.arrow-right.window="isOpen && next()"
            @keydown.arrow-left.window="isOpen && prev()"
        >
            <ul class="chapter-gallery__grid">
                {{-- First cell — a full-bleed photo poster of the latest ride. Its first
                     photo fills the tile and opens the lightbox; the eyebrow, ride title
                     and a single "view all" action sit on a scrim at the bottom, with the
                     calendar tear-off (date it was) peeking from the top corner. A roze
                     hesje of this chapter also gets a quiet "add photos" link. --}}
                <li class="chapter-gallery__cell chapter-gallery__cell--feature">
                    <div class="chapter-latest">
                        <button
                            type="button"
                            class="chapter-latest__media"
                            @click="open(0)"
                            aria-label="Bekijk alle foto's van de laatste rit in {{ $gemeente }}"
                        >
                            <img src="{{ $posterPhoto->getUrl('card') }}" alt="" class="chapter-latest__bg">
                        </button>

                        <time
                            class="chapter-latest__rail"
                            datetime="{{ $latestRide->begin_date->toDateString() }}"
                        >
                            <span class="ride-day__bar" aria-hidden="true"></span>
                            <span class="ride-day__body">
                                <span class="ride-day__day">{{ $rideRail['day'] }}</span>
                                <span class="ride-day__date">{{ $rideRail['num'] }}</span>
                                <span class="ride-day__month">{{ $rideRail['month'] }}</span>
                            </span>
                        </time>

                        <div class="chapter-latest__overlay">
                            <p class="chapter-latest__eyebrow">Laatste rit</p>
                            <h3 class="chapter-latest__title">{{ $latestRide->title }}</h3>
                            <div class="chapter-latest__actions">
                                <x-cta-button variant="blue" size="sm" x-on:click="open(0)">{{ $ridePhotoCount > 1 ? "Bekijk alle {$ridePhotoCount} foto's" : 'Bekijk de foto' }}</x-cta-button>
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
                            @click="open({{ $loop->index + 1 }})"
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

                {{-- The "Mis geen rit" opt-in sits inline in the wall, taking a photo's
                     slot — its narrow column makes the card stack to a calm portrait. --}}
                <li class="chapter-gallery__cell chapter-gallery__cell--optin">
                    <x-newsletter-optin :group="$group" :show-join="true" />
                </li>
            </ul>

            <div
                class="chapter-gallery__lightbox"
                x-show="isOpen"
                x-cloak
                @click.self="close()"
                role="dialog"
                aria-modal="true"
                aria-label="Foto groter bekeken"
            >
                <button type="button" class="chapter-gallery__lb-close" x-ref="closeBtn" @click="close()" aria-label="Sluiten">&times;</button>
                <button type="button" class="chapter-gallery__lb-nav chapter-gallery__lb-nav--prev" @click="prev()" aria-label="Vorige foto">&lsaquo;</button>
                <figure class="chapter-gallery__lb-figure">
                    <img :src="photos[index]?.url" :alt="photos[index]?.name" class="chapter-gallery__lb-img">
                </figure>
                <button type="button" class="chapter-gallery__lb-nav chapter-gallery__lb-nav--next" @click="next()" aria-label="Volgende foto">&rsaquo;</button>
            </div>
        </section>
    @endif

    {{-- 4 · LOCAL EXTRAS — quiet white, above the yellow closing band. Real partners
         (visible, group-scoped) and press articles linked to this group. D-11 closed. --}}
    @if ($hasExtras)
        <section class="chapter-body chapter-body--tail">
            @if ($partners->isNotEmpty() || $pressArticles->isNotEmpty())
                <div class="chapter-extras">
                    @if ($pressArticles->isNotEmpty())
                        {{-- "In de pers" leads the left column (Frederik 2026-06-18):
                             local coverage first, friends to its right. --}}
                        <div class="chapter-extras__block">
                            <h3 class="chapter-section__title">In de pers</h3>
                            <ul class="chapter-press" role="list">
                                @foreach ($pressArticles as $pressArticle)
                                    <li class="chapter-press__item">
                                        <span class="chapter-press__outlet">{{ $pressArticle->outlet }}</span>
                                        @if ($pressArticle->published_at)
                                            <time class="chapter-press__date" datetime="{{ $pressArticle->published_at->toDateString() }}">{{ $pressArticle->published_at->isoFormat('D MMM YYYY') }}</time>
                                        @endif
                                        @if ($pressArticle->url)
                                            <a href="{{ $pressArticle->url }}" target="_blank" rel="noopener noreferrer" class="chapter-press__title">{{ $pressArticle->title }}</a>
                                        @else
                                            <span class="chapter-press__title">{{ $pressArticle->title }}</span>
                                        @endif
                                        @if ($pressArticle->getFirstMedia('document'))
                                            <a href="{{ $pressArticle->getFirstMediaUrl('document') }}" target="_blank" rel="noopener noreferrer" class="chapter-press__doc" aria-label="Artikel downloaden">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                            </a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($partners->isNotEmpty())
                        {{-- One kind of friend, always a text link (never a logo: keeps
                             volunteer-uploaded artwork out). A plain wrapping run that
                             stays compact at any length, with no cap. Right column. --}}
                        <div class="chapter-extras__block">
                            <h3 class="chapter-section__title">Vrienden van de groep</h3>
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
                    @endif

                    {{-- Downloads — chapter flyers/posters. FAUX placeholder for now (Nico
                         wires the source). Sits inside the partners/press gate, so empty
                         chapters stay empty. Compact: icon + label + a small type tag. --}}
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

    {{-- 5 · YELLOW CLOSING BAND — the warmth + "join us" climax, fused with the footer.
         First WHO runs the group (faces), then the "help mee" recruitment CTA whose signup
         form opens on demand right under the button. Rendered in the layout's `closing`
         slot so it sits flush above the yellow footer (main drops to pb-0). --}}
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

                @if ($team->isNotEmpty())
                    {{-- WIE DIT TREKT — full-width carousel of illustrated polaroid cards.
                         Headline + nav on top; crew photo at container width below. Each card's
                         photo slot holds a brand illustration for now (no portrait field yet);
                         a real per-person photo drops into the same <img> later. --}}
                    <div class="chapter-team__carousel"
                        x-data="{ page(dir) { const t = $refs.track; const card = t.querySelector('.chapter-team__card'); if (!card) return; const step = card.offsetWidth + parseFloat(getComputedStyle(t).columnGap || 0); t.scrollBy({ left: dir * step, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }); } }">
                        <div class="chapter-team__head">
                            <h2 class="chapter-team__headline">Wij zwaaien je welkom aan de start</h2>
                            <div class="chapter-team__nav">
                                <button type="button" class="chapter-team__btn" aria-label="Vorige teamleden" x-on:click="page(-1)">‹</button>
                                <button type="button" class="chapter-team__btn" aria-label="Volgende teamleden" x-on:click="page(1)">›</button>
                            </div>
                        </div>

                        <ul class="chapter-team__track" x-ref="track" role="region" aria-label="Team van {{ $gemeente }}">
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
                    </div>
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
