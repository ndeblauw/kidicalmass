<x-roze-hub :group="$group" active="agenda" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl" :own-heading="true">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;
    @endphp

    {{-- AGENDA — drafts lead (the work in progress a hesje can watch take shape), confirmed
         rides follow as a lean list. Drafts = this chapter's unpublished rides; confirmed =
         the published rides on the public agenda. --}}
    <section class="roze-agenda">
        <h1 class="roze-hub-title">{{ __('roze.agenda.title', ['place' => $gemeente]) }}</h1>
        <p class="roze-hub-lead">{{ __('roze.agenda.lead') }}</p>

        @if ($drafts->isNotEmpty())
            <div class="roze-agenda__block">
                <h2 class="roze-agenda__label">{{ __('roze.agenda.preparing') }}</h2>
                <ul role="list" class="roze-agenda__list">
                    @foreach ($drafts as $draft)
                        <li><x-roze-agenda-row :activity="$draft" :group="$group" :draft="true" /></li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="roze-agenda__block">
            <h2 class="roze-agenda__label">{{ __('roze.agenda.confirmed') }}</h2>

            @if ($confirmed->isNotEmpty())
                <ul role="list" class="roze-agenda__list">
                    @foreach ($confirmed as $activity)
                        <li><x-roze-agenda-row :activity="$activity" :group="$group" /></li>
                    @endforeach
                </ul>
            @else
                <p class="roze-agenda__note">{{ __('roze.agenda.empty') }}</p>
            @endif
        </div>
    </section>
</x-roze-hub>
