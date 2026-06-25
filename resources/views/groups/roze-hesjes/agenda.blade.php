<x-roze-hub :group="$group" active="agenda" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;

        $allActivitiesUrl = route('activities.index', ['gemeente' => $group->id]);
    @endphp

    {{-- AGENDA — drafts lead (the work in progress a hesje can watch take shape), confirmed
         rides follow as a lean list. Drafts = this chapter's unpublished rides; confirmed =
         the published rides on the public agenda. --}}
    <section class="roze-agenda">
        <h2 class="roze-hub-title">Agenda van {{ $gemeente }}</h2>
        <p class="roze-hub-lead">Waar de kapiteins nu aan werken, en wat al vastligt.</p>

        @if ($drafts->isNotEmpty())
            <div class="roze-agenda__block">
                <p class="roze-agenda__label">In voorbereiding</p>
                <ul role="list" class="roze-agenda__drafts">
                    @foreach ($drafts as $draft)
                        <li>
                            <a href="{{ route('groups.ride-preview', [$group, 'ride' => $draft->id]) }}" class="roze-draft link-plain">
                                <span class="roze-draft__flag" aria-hidden="true">Nog niet vast</span>
                                <span class="roze-draft__title roze-row-title">{{ $draft->title }}</span>
                                <span class="roze-draft__when">Mogelijk <time datetime="{{ $draft->begin_date->toDateString() }}">{{ $draft->date_full }}</time>, datum nog te bevestigen.</span>
                                <span class="roze-draft__hint">Bekijk hoe deze rit vorm krijgt &rarr;</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="roze-agenda__block">
            <p class="roze-agenda__label">Al vastgelegd</p>

            @if ($confirmed->isNotEmpty())
                <ul role="list" class="roze-agenda__list">
                    @foreach ($confirmed as $activity)
                        @php $rail = \App\Support\RideDate::rail($activity->begin_date); @endphp
                        <li class="roze-agenda-row" style="--ride-accent: {{ $activity->activity_type->accentColor() }};">
                            <time class="roze-agenda-row__date" datetime="{{ $activity->begin_date->toDateString() }}">
                                <span class="roze-agenda-row__num">{{ $rail['num'] }}</span>
                                <span class="roze-agenda-row__mon">{{ $rail['month'] }}</span>
                            </time>
                            <div class="roze-agenda-row__body">
                                <strong class="roze-agenda-row__title roze-row-title">{{ $activity->title }}</strong>
                                <span class="roze-agenda-row__meta">{{ ucfirst($activity->weekday_label) }} &middot; {{ $activity->time_label }}@if (filled($activity->location)) &middot; {{ $activity->location }}@endif</span>
                            </div>
                            <span class="roze-agenda-row__type">{{ $activity->activity_type->labelNl() }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="roze-agenda__note">Nog geen rit vastgelegd. Hou de agenda in de gaten, of plan er samen een in.</p>
            @endif

            <div class="roze-agenda__foot">
                <x-cta-button :href="$allActivitiesUrl" variant="secondary">Alle activiteiten in {{ $gemeente }} (ook voorbije)</x-cta-button>
            </div>
        </div>
    </section>
</x-roze-hub>
