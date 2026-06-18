<x-roze-hub :group="$group" active="agenda" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;

        $agendaByDay = $activities->groupBy(fn ($a) => $a->begin_date->format('Y-m-d'));
        $hasRide = $activities->contains(fn ($a) => $a->activity_type === \App\Enums\ActivityType::KIDICALMASS);

        $allActivitiesUrl = route('activities.index', ['gemeente' => $group->id]);
    @endphp

    {{-- 3 · OP DE AGENDA — straight to the agenda (no intro). Typed, day-grouped on the
         shared ride kit, exactly like the public page. --}}
    <section id="op-de-agenda" class="chapter-agenda">
        <h2 class="roze-hub-title">Op de agenda in {{ $gemeente }}</h2>
        <p class="roze-hub-lead">Wat eraan komt in de buurt. Spring mee op de fiets wanneer het past.</p>

        @unless ($hasRide)
            <p class="roze-agenda__note">Nog geen fietstocht gepland. Hou de agenda in de gaten, of plan er samen een in.</p>
        @endunless

        @if ($activities->isNotEmpty())
            <div class="chapter-agenda__list">
                @foreach ($agendaByDay as $periodKey => $dayActivities)
                    <x-ride-day :period-key="$periodKey" :commune="$gemeente" :rows="$dayActivities->map(fn ($a) => ['item' => $a])->values()->all()" />
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
                <span class="roze-draft__title roze-row-title">Een rit door {{ $gemeente }}, mogelijk 12 juli</span>
                <span class="roze-draft__hint">Bekijk hoe deze rit vorm krijgt →</span>
            </a>
        </div>
    </section>
</x-roze-hub>
