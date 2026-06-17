@props(['group' => null])

@php
    $gemeente = null;
    if ($group) {
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;
    }

    $benefits = [
        'De nieuwste ritten, elke maand als eerste',
        $gemeente ? "Het laatste nieuws uit {$gemeente}" : 'Het laatste nieuws uit jouw lokale groep',
        'Eén rustige mail, makkelijk uit te schrijven',
    ];

    $fieldId = 'newsletter-email-'.\Illuminate\Support\Str::random(6);
@endphp

@auth
    <div {{ $attributes->class('bg-kidical-light-blue rounded-card p-8 flex flex-col gap-3 items-start') }}>
        <h3 class="text-kidical-ink">Je bent al mee</h3>
        <p class="text-kidical-ink/75">Je staat op de hoogte. Je nieuwsvoorkeuren beheer je in je profiel.</p>
        <x-cta-button variant="blue" :href="route('settings')">Beheer voorkeuren</x-cta-button>
    </div>
@else
    <div {{ $attributes->class('@container') }} x-data="{ sent: false }">
        <div class="bg-kidical-light-blue rounded-card p-8 flex flex-col @lg:flex-row gap-8 @lg:items-center">
            <div class="flex flex-col gap-4 @lg:flex-1">
                <h3 class="text-kidical-ink">Blijf op de hoogte</h3>
                <ul class="flex flex-col gap-2.5">
                    @foreach ($benefits as $benefit)
                        <li class="flex items-start gap-2.5 text-kidical-ink">
                            <flux:icon.check-circle variant="solid" class="size-5 text-kidical-blue shrink-0 mt-0.5" aria-hidden="true" />
                            <span>{{ $benefit }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-white rounded-card shadow-card p-6 @lg:flex-1">
                <form @submit.prevent="sent = true" x-show="!sent" class="flex flex-col gap-3">
                    <label for="{{ $fieldId }}" class="text-kidical-ink font-bold">Je e-mailadres</label>
                    <input
                        type="email"
                        id="{{ $fieldId }}"
                        name="email"
                        x-ref="emailField"
                        required
                        autocomplete="email"
                        placeholder="jouw@email.be"
                        class="rounded-full border-2 border-kidical-ink/15 px-4 py-2.5 text-kidical-ink focus:border-kidical-blue focus:outline-none">
                    <x-cta-button variant="blue" x-on:click="$refs.emailField.checkValidity() ? (sent = true) : $refs.emailField.reportValidity()">Ja, hou me op de hoogte</x-cta-button>
                    <p class="text-kidical-ink/60 text-sm">Geen spam, beloofd. Uitschrijven met één klik.</p>
                </form>
                <p x-show="sent" x-cloak class="text-kidical-blue font-bold">Bedankt! Je staat op de lijst.</p>
            </div>
        </div>
    </div>
@endauth
