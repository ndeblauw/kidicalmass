<x-layouts::site title="Kidical Mass {{ $group->name }}">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;

        $agendaByDay = $activities->groupBy(fn ($a) => $a->begin_date->format('Y-m-d'));
        $hasRide = $activities->contains(fn ($a) => $a->activity_type === \App\Enums\ActivityType::KIDICALMASS);

        $allActivitiesUrl = route('activities.index', ['gemeente' => $group->id]);

        // FAUX materials until the per-group materials model lands (Nico / GitHub #37).
        // visibility = the eventual publiek/besloten split: besloten = hesje-only.
        $materials = [
            ['icon' => 'document-text', 'title' => 'Afsprakencharter', 'desc' => 'Onze afspraken voor organisatoren en hesjes.', 'tag' => 'PDF', 'visibility' => 'besloten', 'href' => '#'],
            ['icon' => 'map', 'title' => 'Zo organiseer je een rit', 'desc' => 'Route, gemeentecontact en promo, stap voor stap.', 'tag' => 'Gids', 'visibility' => 'besloten', 'href' => '#'],
            ['icon' => 'megaphone', 'title' => 'De startspeech', 'desc' => 'Het woordje voor de start, voor wie een rit trekt.', 'tag' => 'Voor kapiteins', 'visibility' => 'besloten', 'href' => '#'],
            ['icon' => 'arrow-down-tray', 'title' => 'Posters & promo', 'desc' => 'Affiches en flyers om in je buurt op te hangen.', 'tag' => 'Download', 'visibility' => 'publiek', 'href' => '#'],
            ['icon' => 'arrow-down-tray', 'title' => 'Flyer '.$gemeente.' 2026', 'desc' => 'De lokale flyer om uit te delen.', 'tag' => 'PDF', 'visibility' => 'publiek', 'href' => '#'],
        ];
    @endphp

    {{-- 1 · ROZE HERO — kidical-red band, round group photo + group name (logged-in state) --}}
    <header class="roze-head">
        <img src="{{ asset('img/logos/logo-icon.png') }}" alt="" aria-hidden="true" class="roze-head__daisy">
        <div class="container mx-auto px-4 roze-head__inner">
            <div class="roze-head__layout">
                <div>
                    <p class="roze-head__kicker">Roze hesjes · besloten</p>
                    <h1>Kidical Mass {{ $gemeente }}</h1>
                </div>
                <figure class="roze-head__photo">
                    <img src="{{ asset('img/photography/volunteers/volunteers-pink-vests-with-flag.jpg') }}"
                         alt="Roze hesjes van {{ $gemeente }} aan de start van een Kidical Mass">
                </figure>
            </div>
        </div>
    </header>

    {{-- 2 · WELKOM — compact, time-boxed (per-group cookie, ~first weeks), then auto-hides.
         The same info lives permanently below in "Voor je eerste rit", so nothing is lost. --}}
    @if ($showWelcome)
        <section class="roze-welcome-section">
            <aside class="roze-welcome">
                <p class="roze-welcome__kicker">🎀 Nieuw hier?</p>
                <strong class="roze-welcome__title">Welkom bij de roze hesjes van {{ $gemeente }}!</strong>
                <p class="roze-welcome__body">Fijn dat je meerijdt. Heel even je weg vinden:</p>
                <ul class="roze-welcome__list">
                    <li>Wat een roze hesje doet en hoe je eerste rit verloopt, lees je bij <a href="#voor-je-eerste-rit" class="link-plain">Voor je eerste rit</a>.</li>
                    <li>Je charter, gids en posters staan bij <a href="#jouw-materiaal" class="link-plain">Jouw materiaal</a>.</li>
                    <li>De volgende ritten zie je bovenaan, op de agenda.</li>
                </ul>
                <p class="roze-welcome__foot">Dit welkomstbericht verdwijnt vanzelf na je eerste weken.</p>
            </aside>
        </section>
    @endif

    {{-- LIVING SLOT A · WAT IS NIEUW — the reason to come back: what changed since last visit.
         FAUX feed until a real change-event stream exists (photos added / member joined /
         ride status moved). Backend dep: Nico #37. --}}
    @php
        $feed = [
            ['icon' => 'photo', 'text' => "Drie nieuwe foto's van de rit van vorige zondag.", 'href' => '#fotos'],
            ['icon' => 'user-plus', 'text' => 'Saar rijdt nu mee als roze hesje. Zeg eens hallo.', 'href' => '#de-roze-hesjes'],
            ['icon' => 'map', 'text' => 'De rit van 12 juli krijgt vorm: de route is gekozen.', 'href' => '#op-de-agenda'],
        ];
    @endphp
    <section class="chapter-body roze-whatsup">
        <h2 class="chapter-section__title">Sinds je laatste bezoek</h2>
        <ul role="list" class="roze-whatsup__list">
            @foreach ($feed as $item)
                <li class="roze-whatsup__item">
                    <span class="roze-whatsup__icon" aria-hidden="true">
                        <flux:icon name="{{ $item['icon'] }}" variant="solid" class="size-5" />
                    </span>
                    <a href="{{ $item['href'] }}" class="roze-whatsup__text link-plain">{{ $item['text'] }}</a>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- 3 · OP DE AGENDA — straight to the agenda (no intro). Typed, day-grouped on the
         shared ride kit, exactly like the public page. --}}
    <section id="op-de-agenda" class="chapter-body chapter-agenda">
        <h2 class="chapter-section__title">Op de agenda in {{ $gemeente }}</h2>

        @unless ($hasRide)
            <p class="roze-agenda__note">Nog geen fietstocht gepland. Hou de agenda in de gaten, of plan er samen een in.</p>
        @endunless

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

        {{-- IN VOORBEREIDING — drafts a hesje may peek at (read-only). FAUX single exemplar
             until Activity has lifecycle state (Nico #37). Onboarding-by-visibility. --}}
        <div class="roze-drafts">
            <p class="roze-drafts__label">In voorbereiding</p>
            <a href="{{ route('groups.ride-preview', $group) }}" class="roze-draft link-plain">
                <span class="roze-draft__flag" aria-hidden="true">Nog niet vast</span>
                <span class="roze-draft__title">Een rit door {{ $gemeente }} — mogelijk 12 juli</span>
                <span class="roze-draft__hint">Bekijk hoe deze rit vorm krijgt →</span>
            </a>
        </div>
    </section>

    {{-- LIVING SLOT B · FOTO'S — shared chapter album + upload. FAUX shell: Group is not yet
         HasMedia, there is no group gallery. Backend dep: Nico #37 (Group media library). --}}
    <section id="fotos" class="chapter-body roze-gallery scroll-mt-24">
        <div class="roze-gallery__head">
            <h2 class="chapter-section__title">Foto's van het chapter</h2>
            <button type="button" class="roze-gallery__upload" disabled aria-disabled="true">
                <flux:icon name="arrow-up-tray" variant="micro" class="size-4" /> Foto's toevoegen (binnenkort)
            </button>
        </div>
        <p class="roze-gallery__lead">Het gedeelde album van {{ $gemeente }}. Hier komen de foto's van onze ritten samen.</p>
        <ul role="list" class="roze-gallery__grid">
            @for ($i = 0; $i < 6; $i++)
                <li class="roze-gallery__cell" aria-hidden="true"></li>
            @endfor
        </ul>
    </section>

    {{-- 4 · DE ROZE HESJES — the full roster (replaces the public kapiteins section).
         Everyone is visible to fellow hesjes, regardless of their public opt-in. --}}
    <section id="de-roze-hesjes" class="roze-roster-band">
        <div class="container mx-auto px-4">
            <h2 class="chapter-section__title">De roze hesjes van {{ $gemeente }}</h2>
            <ul role="list" class="roze-roster">
                @foreach ($roster as $member)
                    <li class="roze-roster__member">
                        <span class="roze-roster__avatar" aria-hidden="true">{{ $member->initials() }}</span>
                        <div class="min-w-0">
                            <strong class="roze-roster__name">{{ $member->name }}</strong>
                            <span class="roze-roster__role">{{ $member->pivot->role === 'captain' ? 'Kapitein' : 'Roze hesje' }}</span>
                        </div>
                        @if ($member->pivot->created_at && $member->pivot->created_at->greaterThan($newMemberCutoff))
                            <span class="roze-roster__new">Nieuw</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- 5 · VOOR JE EERSTE RIT — permanent onboarding (always here, so the welcome block's
         info stays findable after it expires). The startspeech is NOT here — it is kapitein
         material (a besloten tile in Jouw materiaal). --}}
    <section id="voor-je-eerste-rit" class="roze-onboarding scroll-mt-24">
        <div class="container mx-auto px-4">
            <h2 class="chapter-section__title">Voor je eerste rit</h2>

            <h3 class="roze-onboarding__sub">Wat doet een roze hesje?</h3>
            <div class="roze-onboarding__cards">
                <x-feature-card icon="users" title="Je rijdt mee met de groep" color="red">
                    Je fietst naast de kinderen en houdt ze samen. Geen kopwerk, gewoon meerijden en mee opletten.
                </x-feature-card>
                <x-feature-card icon="sparkles" title="Je brengt rust en goeie energie" color="orange">
                    Een vrolijke, kalme aanwezigheid op de weg doet meer dan je denkt. Dat ben jij.
                </x-feature-card>
                <x-feature-card icon="eye" title="Goed zichtbaar zijn is genoeg" color="blue">
                    Een fluo hesje en een glimlach. Meer heb je niet nodig om het verschil te maken.
                </x-feature-card>
                <x-feature-card icon="academic-cap" title="Geen verkeersopleiding nodig" color="green">
                    Dat leer je vanzelf, samen met het team. Je staat er nooit alleen voor.
                </x-feature-card>
            </div>

            {{-- Begeleidingsvideo — embedded inline (privacy-friendly nocookie host). --}}
            <figure class="roze-video">
                <iframe
                    src="https://www.youtube-nocookie.com/embed/i9YQxJ-ChNM"
                    title="Veilig begeleiden als roze hesje"
                    loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                ></iframe>
            </figure>

            <h3 class="roze-onboarding__sub">Je eerste rit, stap voor stap</h3>
            <ol role="list" class="roze-steps">
                @foreach ([
                    ['Voor de start', 'De hesjes zitten in een gemeenschappelijke tas en worden ter plaatse uitgedeeld. Je hoeft zelf niks mee te brengen.'],
                    ['Onderweg', 'Vooraan rijdt een kapitein, achteraan een sluiter. Jij rijdt mee in de groep en houdt mee alles samen.'],
                    ['Het tempo', 'We rijden op kindertempo, ongeveer 8 à 9 km per uur. Rustig aan, het is geen koers.'],
                    ['Aan de kruispunten', 'We zetten ze samen veilig af zodat de groep kan passeren, en sluiten daarna weer aan.'],
                ] as $i => $step)
                    <li class="roze-step">
                        <span class="roze-step__num">{{ $i + 1 }}</span>
                        <div>
                            <strong class="roze-step__title">{{ $step[0] }}</strong>
                            <p class="roze-step__body">{{ $step[1] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- 6 · JOUW MATERIAAL — the chapter's material library (replaces the public CTA).
         FAUX until the backend lands. The startspeech is a besloten "Voor kapiteins" tile. --}}
    <section id="jouw-materiaal" class="chapter-body roze-materials-section scroll-mt-24">
        <h2 class="chapter-section__title">Jouw materiaal</h2>
        <p class="roze-materials__lead">Alles op één plek. <strong>Besloten</strong> blijft bij de hesjes; <strong>publiek</strong> mag je vrij delen.</p>
        <div class="roze-materials">
            @foreach ($materials as $material)
                @php $external = \Illuminate\Support\Str::startsWith($material['href'], 'http'); @endphp
                <a href="{{ $material['href'] }}" @if ($external) target="_blank" rel="noopener" @endif class="roze-material link-plain">
                    <span class="roze-material__icon roze-material__icon--{{ $material['visibility'] }}" aria-hidden="true">
                        <flux:icon name="{{ $material['icon'] }}" variant="solid" class="size-6" />
                    </span>
                    <strong class="roze-material__title">{{ $material['title'] }}</strong>
                    <span class="roze-material__desc">{{ $material['desc'] }}</span>
                    <span class="roze-material__tags">
                        <span class="roze-material__tag">{{ $material['tag'] }}</span>
                        <span class="roze-material__badge roze-material__badge--{{ $material['visibility'] }}">{{ $material['visibility'] === 'besloten' ? 'Besloten' : 'Publiek' }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- WHATSAPP-DOORGANG — deliberate hand-off to the chatter, kept apart from the page so
         "stand van zaken" and "gesprek" don't try to be each other. FAUX href until a per-group
         whatsapp URL exists (Nico #37). --}}
    <section class="chapter-body roze-whatsapp">
        <div class="roze-whatsapp__inner">
            <div>
                <strong class="roze-whatsapp__title">De WhatsApp-groep van {{ $gemeente }}</strong>
                <p class="roze-whatsapp__body">Voor het dagelijkse gepraat, snelle vragen en "wie kan er zondag mee".</p>
            </div>
            <a href="#" class="roze-whatsapp__btn" aria-disabled="true">Naar WhatsApp →</a>
        </div>
    </section>

</x-layouts::site>
