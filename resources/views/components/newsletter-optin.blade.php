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
        ? "Eén rustige mail per maand met de ritten en het nieuws uit {$gemeente}."
        : 'Eén rustige mail per maand met de ritten bij jou in de buurt.';
@endphp

@auth
    <div {{ $attributes->class('bg-kidical-light-blue rounded-card p-8 flex flex-col gap-3 items-start') }}>
        <h3 class="text-kidical-ink">Je bent al mee</h3>
        <p class="text-kidical-ink/75">Je staat op de hoogte. Je nieuwsvoorkeuren beheer je in je profiel.</p>
        <x-cta-button variant="blue" :href="route('settings')">Beheer voorkeuren</x-cta-button>
    </div>
@else
    <div {{ $attributes->class('bg-kidical-light-blue rounded-card p-8 flex flex-col gap-4 items-start') }}>
        <h3 class="text-kidical-ink">Mis geen rit</h3>
        <p class="text-kidical-ink/75">{{ $lead }}</p>
        {{-- TODO: point at the dedicated sign-up page once it exists (placeholder #). --}}
        <x-cta-button variant="blue" href="#">Schrijf je in</x-cta-button>
    </div>
@endauth
