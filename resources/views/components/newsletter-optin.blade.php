@props(['group' => null, 'showJoin' => false, 'prominent' => false])

@php
    $gemeente = null;
    if ($group) {
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;
    }

    // Teaser only: the actual sign-up form lives on its own page. This block just
    // makes the promise and sends people onward.
    $lead = $gemeente
        ? "Eén mail per maand met de ritten en het nieuws uit {$gemeente}."
        : 'Eén mail per maand met de ritten bij jou in de buurt.';

    // Prominent treatment (chapter page): the opt-in is the page's primary low-commitment
    // CTA, so it lifts off the white with the card shadow and trades the quiet outlined
    // button for the signature pill. Default (calendar sidebar) stays calm and flat.
    $cardClass = '@container bg-kidical-light-blue rounded-card p-8'.($prominent ? ' shadow-card' : '');
    $ctaVariant = $prominent ? 'yellow' : 'secondary';
@endphp

@auth
    <div {{ $attributes->class($cardClass) }}>
        @if ($showJoin)
            {{-- A logged-in follower is the warmest lead on the page. Don't dead-end on
                 "manage settings": acknowledge the follow, then escalate to the next step
                 (join the crew). Follow first, then join. The button scrolls to the join
                 band and opens its form via the open-volunteer event. --}}
            <div class="flex flex-col gap-4 items-start @xl:flex-row @xl:items-center @xl:justify-between @xl:gap-8">
                <div class="flex flex-col gap-2">
                    <h3 class="text-kidical-ink">Meer dan meefietsen?</h3>
                    <p class="text-kidical-ink/75">{{ $gemeente ? 'Zin om zelf mee te trekken in '.$gemeente.'?' : 'Zin om zelf mee te helpen?' }}</p>
                </div>
                <x-cta-button variant="blue" icon="heart" href="#aanmelden" x-data="{}" x-on:click="$dispatch('open-volunteer')" class="shrink-0">Word vrijwilliger</x-cta-button>
            </div>
        @else
            <div class="flex flex-col gap-3 items-start @xl:flex-row @xl:items-center @xl:justify-between @xl:gap-8">
                <div class="flex flex-col gap-3">
                    <h3 class="text-kidical-ink">Je bent al mee</h3>
                    <p class="text-kidical-ink/75">Je staat op de hoogte. Je nieuwsvoorkeuren beheer je in je profiel.</p>
                </div>
                <x-cta-button variant="blue" :href="route('settings')" class="shrink-0">Beheer voorkeuren</x-cta-button>
            </div>
        @endif
    </div>
@else
    <div {{ $attributes->class($cardClass) }}>
        <div class="flex flex-col gap-4 items-start @xl:flex-row @xl:items-center @xl:justify-between @xl:gap-8">
            <div class="flex flex-col gap-3">
                <h3 class="text-kidical-ink">Mis geen rit</h3>
                <p class="text-kidical-ink/75">{{ $lead }}</p>
            </div>
            <x-cta-button :variant="$ctaVariant" :href="route('newsletter.show', ['locale' => app()->getLocale()])" class="shrink-0">Schrijf je in</x-cta-button>
        </div>
    </div>
@endauth
