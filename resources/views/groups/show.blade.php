{{--
    Chapter page (P-11) — the local group's HOME. Family-first arc (Critique v3,
    Frederik 2026-06-03): header+identity -> ride photo -> ONE typed agenda (rides/
    workshops/meetings, ride weighted) + "mis geen rit" opt-in -> team (lead + crew,
    with roles) + ON-DEMAND meehelpen form -> extras -> closing. National news CUT.
    NL, on the ride/show kit. Structure only here; appearance in app.css.
    Faked until the backend lands: active volunteers + roles, friends, downloads,
    the opt-in (client-side). Real now: typed agenda, filtered "alle activiteiten"
    deep-link (?gemeente=id), on-demand reveal, J2 form.
    Plan: docs/wiki/design/30-skeleton/chapters.md (§ Chapter Page + Critique v3).
--}}
<x-layouts::site title="{{ $group->name }}">
    @php
        $regionLabels = [
            'Brussels Capital Region' => 'Brussel',
            'Wallonia' => 'Wallonië',
            'Flanders' => 'Vlaanderen',
        ];
        $region = $group->parent ? ($regionLabels[$group->parent->name] ?? $group->parent->name) : null;

        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;

        // The agenda is type-aware: the next ride (kidicalmass) is the weighted hero;
        // everything else (later rides, workshops, meetings) lists below, type-labelled.
        $nextRide = $activities->firstWhere('activity_type', \App\Enums\ActivityType::KIDICALMASS);
        $rest = $nextRide ? $activities->reject(fn ($a) => $a->id === $nextRide->id)->values() : $activities;

        $leadNames = $group->users->pluck('name')->map(fn ($n) => explode(' ', trim($n))[0])->filter()->values();

        // Per-type NL framing (family-facing) — label, CTA, colour modifier.
        $typeMeta = fn (\App\Enums\ActivityType $t) => match ($t) {
            \App\Enums\ActivityType::KIDICALMASS => ['label' => 'Fietstocht', 'cta' => 'Naar de fietstocht →', 'mod' => 'ride', 'quiet' => false],
            \App\Enums\ActivityType::WORKSHOP => ['label' => 'Workshop', 'cta' => 'Meer info →', 'mod' => 'workshop', 'quiet' => false],
            \App\Enums\ActivityType::MEETING => ['label' => 'Voor vrijwilligers', 'cta' => 'Meer info →', 'mod' => 'meeting', 'quiet' => true],
            default => ['label' => 'Activiteit', 'cta' => 'Meer info →', 'mod' => 'other', 'quiet' => false],
        };

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

        // FAUX local extras (no per-group partners/downloads model yet). Press never faked (D-11).
        $fauxFriends = [
            ['name' => 'Fietsbieb '.$gemeente],
            ['name' => 'Buurthuis om de hoek'],
        ];
        $fauxDownloads = [
            ['label' => 'Flyer '.$gemeente.' 2026 (PDF)', 'url' => '#'],
        ];

        $allActivitiesUrl = route('activities.index', ['gemeente' => $group->id]);
    @endphp

    {{-- 1 · IDENTITY HEADER — system blue band (mirrors .index-hero on the directory) --}}
    <header class="chapter-head">
        <img src="{{ asset('img/logos/logo-icon.png') }}" alt="" aria-hidden="true" class="chapter-head__daisy">
        <div class="container mx-auto px-4 chapter-head__inner">
            <p class="chapter-head__crumb">
                <a href="{{ route('groups.index') }}" class="link-plain">← Lokale groepen</a>
                @if ($region)<span class="chapter-head__region">· {{ $region }}</span>@endif
            </p>
            <h1>{{ $group->name }}</h1>
            <p class="chapter-head__intro">
                De buurtfietstocht voor en door {{ $gemeente }}. Rustig tempo, afgezette straten, iedereen welkom, van kinderfiets tot bakfiets.
            </p>
        </div>
    </header>

    {{-- 2 · WARM RIDE PHOTO — shared fallback (no per-group cover field yet) --}}
    <figure class="chapter-photo">
        <img
            src="{{ asset('img/photography/ride-cinquantenaire-crowd.jpg') }}"
            alt="Gezinnen op een Kidical Mass fietstocht door afgezette straten"
            class="chapter-photo__img"
        >
    </figure>

    <div class="container mx-auto px-4 chapter-body">

        {{-- 3 · OP DE AGENDA — one typed agenda; the next ride is the weighted hero --}}
        <section class="chapter-agenda">
            <h2 class="chapter-section__title">Op de agenda in {{ $gemeente }}</h2>

            @if ($nextRide)
                <article class="chapter-next__card">
                    <p class="chapter-next__date">
                        <time datetime="{{ $nextRide->begin_date->format('Y-m-d\TH:i') }}">
                            {{ ucfirst($nextRide->begin_date->locale('nl')->isoFormat('dddd D MMMM · HH:mm')) }}
                        </time>
                    </p>
                    <h3 class="chapter-next__title">{{ $nextRide->title_nl }}</h3>
                    @if ($nextRide->location)
                        <p class="chapter-next__loc">
                            <flux:icon.map-pin variant="solid" aria-hidden="true" />
                            Verzamelen: {{ $nextRide->location }}
                        </p>
                    @endif
                    <p class="chapter-next__reassure">Rustig tempo · kinderen voorop · iedereen welkom</p>
                    <a href="{{ route('activities.show', $nextRide) }}" class="chapter-next__cta link-plain">Naar de fietstocht →</a>
                </article>
            @else
                {{-- Designed empty state: no upcoming RIDE. Warm, not a dead end. Workshops /
                     meetings (if any) still list below — honest, never mislabelled as a ride. --}}
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
            @endif

            {{-- The rest of the agenda: later rides, workshops, meetings — each type-labelled --}}
            @if ($rest->isNotEmpty())
                <ul class="chapter-agenda__list">
                    @foreach ($rest as $activity)
                        @php $m = $typeMeta($activity->activity_type); @endphp
                        <x-agenda-item
                            :badge="$m['label']"
                            :badge-variant="$m['mod']"
                            :datetime="$activity->begin_date->format('Y-m-d\TH:i')"
                            :when="$activity->begin_date->locale('nl')->isoFormat('dd D MMM · HH:mm')"
                            :title="$activity->title_nl"
                            :location="$activity->location"
                            :cta-href="route('activities.show', $activity)"
                            :cta-label="$m['cta']"
                            :quiet="$m['quiet']"
                        />
                    @endforeach
                </ul>
            @endif

            @if ($activities->isNotEmpty())
                <a href="{{ $allActivitiesUrl }}" class="chapter-next__all link-plain">Alle activiteiten in {{ $gemeente }} (ook voorbije) →</a>
            @endif

            {{-- Promoted "mis geen rit" opt-in — calm, right by the agenda (was buried at the
                 foot under the news). Faked: client-side only, does not persist. Only when
                 there IS a ride; the empty state already carries its own opt-in above. --}}
            @if ($nextRide)
                <div class="chapter-agenda__notify" x-data="{ sent: false }">
                    <p class="chapter-agenda__notify-title">Mis geen enkele rit in {{ $gemeente }}</p>
                    <form @submit.prevent="sent = true" class="chapter-notify" x-show="!sent">
                        <label class="sr-only" for="notify-agenda">E-mail</label>
                        <input type="email" id="notify-agenda" required placeholder="jouw@email.be" class="chapter-notify__input">
                        <button type="submit" class="chapter-notify__btn">Hou me op de hoogte</button>
                    </form>
                    <p class="chapter-notify__done" x-show="sent" x-cloak>Bedankt! Je hoort het zodra de volgende rit bekend is.</p>
                </div>
            @endif
        </section>

        {{-- 4 · WIE DIT TREKT — lead + active volunteers (with roles), then an ON-DEMAND invite --}}
        <section class="chapter-team" id="aanmelden">
            <h2 class="chapter-section__title">Wie dit trekt</h2>

            @if ($team->isNotEmpty())
                <ul class="chapter-team__faces">
                    @foreach ($team as $member)
                        <li class="chapter-team__face">
                            <span class="chapter-team__avatar" aria-hidden="true">{{ $member['initials'] }}</span>
                            <span class="chapter-team__name">{{ explode(' ', trim($member['name']))[0] }}</span>
                            <span class="chapter-team__role">{{ $member['role'] }}</span>
                        </li>
                    @endforeach
                </ul>

                {{-- On-demand reveal (mirrors the event-detail volunteer pattern). The family
                     page stays light; the helper expands the form. Auto-open via ?intent=volunteer. --}}
                <div class="chapter-join scroll-mt-24" x-data="{ open: {{ request('intent') === 'volunteer' ? 'true' : 'false' }} }">
                    <div class="chapter-join__pitch" x-show="!open">
                        <h3 class="chapter-team__pitch-title">Help mee in {{ $gemeente }}</h3>
                        <p class="chapter-team__pitch">
                            Een paar uur per maand, samen met {{ $leadNames->join(', ', ' & ') }}. Je hoeft geen fietsexpert te zijn. Gewoon iemand die zijn buurt graag ziet.
                        </p>
                        <div class="chapter-join__actions">
                            <button type="button" x-on:click="open = true" class="chapter-join__btn">Ja, ik wil meehelpen</button>
                            <a href="{{ route('volunteer') }}" class="chapter-team__more link-plain">Meer over meehelpen →</a>
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
                <h3 class="chapter-team__pitch-title">Help {{ $gemeente }} op gang</h3>
                <p class="chapter-team__pitch">
                    Er is nog geen team. Iemand moet de eerste zijn, en dat hoef je niet alleen te doen. Laat hieronder van je horen, wij helpen je op weg.
                </p>
                <div class="chapter-team__form scroll-mt-24" id="aanmelden">
                    <livewire:chapter-volunteer-signup :group="$group" />
                    <p class="chapter-team__more"><a href="{{ route('volunteer') }}" class="link-plain">Meer over meehelpen →</a></p>
                </div>
            @endif
        </section>

        {{-- 5 · LOCAL EXTRAS — faux vrienden + downloads (preview). Press is hide-if-empty,
             never faked (no per-group press model — D-11). National news was CUT (Critique v3). --}}
        @if (! empty($fauxFriends) || ! empty($fauxDownloads))
            <section class="chapter-extras">
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
            </section>
        @endif

        {{-- Parent/region node: minimal children list so a parent page does not break. --}}
        @if ($group->children->isNotEmpty())
            <section class="chapter-children">
                <h2 class="chapter-section__title">Lokale groepen in {{ $group->name }}</h2>
                <ul class="flex flex-wrap gap-2.5">
                    @foreach ($group->children as $child)
                        <li><a href="{{ route('groups.show', $child) }}" class="grp-pill link-plain">{{ $child->name }}</a></li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>

    {{-- 6 · CLOSING BEAT — hand the group back (the opt-in moved up by the agenda). --}}
    <section class="chapter-close">
        <div class="container mx-auto px-4 chapter-close__inner">
            <p class="chapter-close__back">
                <a href="{{ route('groups.index') }}" class="link-plain">Deze groep is van jou. ← Alle lokale groepen</a>
            </p>
        </div>
    </section>

    <x-slot:closing>
        <x-closing-cta heading="Rij mee in je buurt"
            :href="route('membership')" label="Word lid" icon="heart" />
    </x-slot:closing>
</x-layouts::site>
