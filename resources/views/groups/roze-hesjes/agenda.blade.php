<x-roze-hub :group="$group" active="agenda" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;
    @endphp

    {{-- AGENDA — drafts lead (the work in progress a hesje can watch take shape), confirmed
         rides follow as a lean list. Drafts = this chapter's unpublished rides; confirmed =
         the published rides on the public agenda. --}}
    <section class="roze-agenda">
        <h2 class="roze-hub-title">Agenda van {{ $gemeente }}</h2>
        <p class="roze-hub-lead">Waar de kapiteins nu aan werken, en wat al vastligt.</p>

        @if ($drafts->isNotEmpty())
            <div class="roze-agenda__block">
                <h3 class="roze-agenda__label">In voorbereiding</h3>
                <ul role="list" class="roze-agenda__list">
                    @foreach ($drafts as $draft)
                        <li><x-roze-agenda-row :activity="$draft" :group="$group" :draft="true" /></li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="roze-agenda__block">
            <h3 class="roze-agenda__label">Al vastgelegd</h3>

            @if ($confirmed->isNotEmpty())
                <ul role="list" class="roze-agenda__list">
                    @foreach ($confirmed as $activity)
                        <li><x-roze-agenda-row :activity="$activity" :group="$group" /></li>
                    @endforeach
                </ul>
            @else
                <p class="roze-agenda__note">Nog geen rit vastgelegd. Hou de agenda in de gaten, of plan er samen een in.</p>
            @endif
        </div>
    </section>
</x-roze-hub>
