@props(['group' => null])

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
@endphp

@auth
    <div {{ $attributes->class('@container bg-kidical-light-blue rounded-card p-8') }}>
        <div class="flex flex-col gap-3 items-start @xl:flex-row @xl:items-center @xl:justify-between @xl:gap-8">
            <div class="flex flex-col gap-3">
                <h3 class="text-kidical-ink">Je bent al mee</h3>
                <p class="text-kidical-ink/75">Je staat op de hoogte. Je nieuwsvoorkeuren beheer je in je profiel.</p>
            </div>
            <x-cta-button variant="blue" :href="route('settings')" class="shrink-0">Beheer voorkeuren</x-cta-button>
        </div>
    </div>
@else
    <div {{ $attributes->class('@container bg-kidical-light-blue rounded-card p-8') }}>
        <div class="flex flex-col gap-4 items-start @xl:flex-row @xl:items-center @xl:justify-between @xl:gap-8">
            <div class="flex flex-col gap-4">
                <h3 class="text-kidical-ink">Mis geen rit</h3>
                <p class="text-kidical-ink/75">{{ $lead }}</p>
            </div>
            <x-cta-button variant="secondary" :href="route('newsletter.show', ['locale' => app()->getLocale()])" class="shrink-0">Schrijf je in</x-cta-button>
        </div>
    </div>
@endauth
