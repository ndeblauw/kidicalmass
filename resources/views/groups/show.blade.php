{{--
    Chapter page (P-11) — the local group's HOME. Family-first arc, re-arranged onto the
    shared band rhythm (Critique + normalize, Frederik 2026-06-09): blue identity hero with
    a group photo (inner-page <x-page-hero> layout) -> ONE white agenda (illustration column +
    typed list, every activity day-grouped on the shared ride kit) -> quiet white extras
    (vrienden + downloads) -> ONE
    yellow CLOSING band that fuses with the footer: first WHO runs the group (faces), then
    the "help mee" recruitment CTA whose signup form opens on demand right under the button.
    Colour story blue -> white -> yellow, like the sibling pages. The yellow band lives in
    the layout's `closing` slot so it sits flush against the yellow footer (main -> pb-0).
    NL, on the ride/show kit. Structure only here; appearance in resources/css/pages/chapters.css.
    Faked until the backend lands: friends, downloads, the empty-state opt-in (client-side).
    Real now: typed agenda, filtered "alle activiteiten" deep-link (?gemeente=id), on-demand
    reveal, group-specific J2 signup form.
    Plan: docs/wiki/design/30-skeleton/chapters.md (§ Chapter Page + Critique v3).
--}}
<x-layouts::site title="{{ $group->name }}">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;

        // Hero lockup: a "Kidical Mass" brand line above the gemeente, but only for groups
        // that actually carry the prefix (parent/region nodes show their bare name on one line).
        $hasBrandPrefix = (bool) preg_match('/^\s*kidical\s+mass\s+/i', $group->name);
        // Eyebrow = postcode; falls back for nodes without a zip (real field is `zip`).
        $heroEyebrow = filled($group->zip) ? $group->zip : 'Lokale groep';

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

        // FAUX local extras (no per-group partners/downloads model yet). Press never faked (D-11).
        $fauxFriends = [
            ['name' => 'Fietsbieb '.$gemeente],
            ['name' => 'Buurthuis om de hoek'],
        ];
        $fauxDownloads = [
            ['label' => 'Flyer '.$gemeente.' 2026 (PDF)', 'url' => '#'],
        ];

        $hasExtras = ! empty($fauxFriends) || ! empty($fauxDownloads) || $group->children->isNotEmpty();

        $allActivitiesUrl = route('activities.index', ['gemeente' => $group->id]);
    @endphp

    {{-- 1 · IDENTITY HERO — full-bleed blue band on the shared inner-page rhythm (mirrors
         <x-page-hero>): postcode eyebrow, two-line name, and a group/team photo where sibling
         pages put an illustration. Photo is a shared placeholder for now; a real per-group
         photo drops into the same <img> once the backend lands (no per-group cover field yet). --}}
    <header class="chapter-head">
        <div class="container mx-auto px-4 chapter-head__inner">
            <div class="chapter-head__copy">
                <p class="chapter-head__eyebrow">{{ $heroEyebrow }}</p>
                <h1 class="chapter-head__title">
                    @if ($hasBrandPrefix)
                        Kidical Mass<br>{{ $gemeente }}
                    @else
                        {{ $group->name }}
                    @endif
                </h1>
            </div>
            <figure class="chapter-head__media">
                <img
                    src="{{ asset('img/photography/ride-cinquantenaire-crowd.jpg') }}"
                    alt="Het team en de fietsers van Kidical Mass {{ $gemeente }}"
                    class="chapter-head__photo"
                >
            </figure>
        </div>
    </header>

    {{-- 2 · OP DE AGENDA — white. A decorative illustration leads a left column; the agenda
         (typed, day-grouped on the shared ride kit) fills the wider right column. --}}
    <section class="chapter-body chapter-agenda">
        <div class="chapter-agenda__grid">
            <aside class="chapter-agenda__aside" aria-hidden="true">
                <img src="{{ asset('img/illustrations/heart-30-sign.svg') }}" alt="" class="chapter-agenda__illustration">
            </aside>

            <div class="chapter-agenda__main">
                <h2 class="chapter-section__title">Op de agenda in {{ $gemeente }}</h2>

                {{-- No ride on the agenda yet (there may still be workshops/meetings below):
                     a warm note, not a dead end. Honest — never a workshop dressed as a ride. --}}
                @unless ($hasRide)
                    <div class="chapter-next__card chapter-next__card--empty" x-data="{ sent: false }">
                        <p class="chapter-next__empty-lead">Nog geen fietstocht gepland.</p>
                        <p class="chapter-next__empty-body">Laat je e-mail achter en je bent de eerste die het hoort als {{ $gemeente }} vertrekt.</p>
                        <form @submit.prevent="sent = true" class="chapter-notify" x-show="!sent">
                            <label class="sr-only" for="notify-empty">E-mail</label>
                            <input type="email" id="notify-empty" required placeholder="jouw@email.be" class="chapter-notify__input">
                            <button type="submit" class="chapter-notify__btn">Hou me op de hoogte</button>
                        </form>
                        <p class="chapter-notify__done" x-show="sent" x-cloak>Bedankt! We laten het je weten zodra er een rit is.</p>
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

                    <div class="chapter-agenda__foot">
                        <x-cta-button :href="$allActivitiesUrl" variant="secondary">Alle activiteiten in {{ $gemeente }} (ook voorbije)</x-cta-button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- 3 · LOCAL EXTRAS — quiet white, moved above the closing band. Faux vrienden +
         downloads (preview). Press is hide-if-empty, never faked (D-11). National news CUT. --}}
    @if ($hasExtras)
        <section class="chapter-body chapter-body--tail">
            @if (! empty($fauxFriends) || ! empty($fauxDownloads))
                <div class="chapter-extras">
                    @if (! empty($fauxFriends))
                        <div class="chapter-extras__block">
                            <h2 class="chapter-section__title">Vrienden van de groep</h2>
                            <ul class="chapter-extras__friends">
                                @foreach ($fauxFriends as $friend)
                                    <li>{{ $friend['name'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (! empty($fauxDownloads))
                        <div class="chapter-extras__block">
                            <h2 class="chapter-section__title">Downloads</h2>
                            <ul class="chapter-extras__downloads">
                                @foreach ($fauxDownloads as $download)
                                    <li><a href="{{ $download['url'] }}" class="link-plain">↓ {{ $download['label'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Parent/region node: minimal children list so a parent page does not break. --}}
            @if ($group->children->isNotEmpty())
                <div class="chapter-children">
                    <h2 class="chapter-section__title">Lokale groepen in {{ $group->name }}</h2>
                    <ul class="flex flex-wrap gap-2.5">
                        @foreach ($group->children as $child)
                            <li><a href="{{ route('groups.show', $child) }}" class="grp-pill link-plain">{{ $child->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    @endif

    {{-- 4 · YELLOW CLOSING BAND — the warmth + "join us" climax, fused with the footer.
         First WHO runs the group (faces), then the "help mee" recruitment CTA whose signup
         form opens on demand right under the button. Rendered in the layout's `closing`
         slot so it sits flush above the yellow footer (main drops to pb-0). --}}
    <x-slot:closing>
        <section class="chapter-team-band" id="aanmelden">
            <div class="container mx-auto px-4 chapter-team-band__inner">
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

                    <figure class="chapter-team-band__media chapter-team-band__media--below">
                        <img src="{{ asset('img/photography/volunteers/volunteers-pink-vests-with-flag.jpg') }}"
                            alt="Vrijwilligers in hesjes zwaaien blij met de Kidical Mass-vlag tijdens een rit" loading="lazy">
                    </figure>

                    {{-- HELP MEE — recruitment CTA. On-demand reveal: the band stays light,
                         the helper expands the group-specific form right under the button.
                         Auto-open via ?intent=volunteer. --}}
                    <div class="chapter-join scroll-mt-24" x-data="{ open: {{ request('intent') === 'volunteer' ? 'true' : 'false' }} }">
                        <div class="chapter-join__cta" x-show="!open">
                            <h2>Help mee in {{ $gemeente }}</h2>
                            <div class="chapter-join__actions">
                                <x-cta-button variant="blue" icon="heart" href="#aanmelden" x-on:click.prevent="open = true">Ja, ik wil meehelpen</x-cta-button>
                                <x-cta-button variant="secondary" href="{{ route('volunteer') }}">Meer over meehelpen</x-cta-button>
                            </div>
                        </div>

                        <div class="chapter-join__form" x-show="open" x-cloak>
                            <button type="button" x-on:click="open = false" class="chapter-join__back">← Terug</button>
                            @if (request('intent') === 'volunteer')
                                <p class="chapter-team__welcome">
                                    Je komt meehelpen in {{ $group->name }}. Welkom! Laat hieronder je gegevens achter.
                                </p>
                            @endif
                            <livewire:chapter-volunteer-signup :group="$group" />
                        </div>
                    </div>
                @else
                    {{-- Empty-team state: stays warm, the form leads (the absence is an invitation). --}}
                    <div class="chapter-team-band__top">
                        <figure class="chapter-team-band__media">
                            <img src="{{ asset('img/photography/volunteers/volunteers-pink-vests-with-flag.jpg') }}"
                                alt="Vrijwilligers in hesjes zwaaien blij met de Kidical Mass-vlag tijdens een rit" loading="lazy">
                        </figure>
                        <div class="chapter-team-band__intro">
                            <h2 class="chapter-section__title">Help {{ $gemeente }} op gang</h2>
                            <p class="chapter-team__pitch">
                                Er is nog geen team. Iemand moet de eerste zijn, en dat hoef je niet alleen te doen. Laat hieronder van je horen, wij helpen je op weg.
                            </p>
                        </div>
                    </div>
                    <div class="chapter-join chapter-join--open scroll-mt-24">
                        <livewire:chapter-volunteer-signup :group="$group" />
                        <p class="chapter-team__more"><a href="{{ route('volunteer') }}" class="link-plain">Meer over meehelpen →</a></p>
                    </div>
                @endif
            </div>
        </section>
    </x-slot:closing>
</x-layouts::site>
