<div>
    @auth
        <div class="bg-kidical-light-blue rounded-card p-8 flex flex-col gap-4 items-start">
            <h2 class="text-kidical-ink">Je bent al mee</h2>
            <p class="text-kidical-ink/75">Je staat op de hoogte. Je nieuwsvoorkeuren beheer je in je profiel.</p>
            <x-cta-button variant="blue" :href="route('settings')">Beheer voorkeuren</x-cta-button>
        </div>
    @elseif ($submitted)
        <div class="bg-white rounded-card shadow-card p-8 flex flex-col gap-4 items-start" role="status">
            <h2>Je bent erbij!</h2>
            <p>De volgende nieuwsbrief komt naar <strong>{{ $email }}</strong>. Kan je niet wachten? De kalender staat al online.</p>
            <x-cta-button variant="yellow" icon="arrow" :href="route('activities.index', ['locale' => app()->getLocale()])">Bekijk de kalender</x-cta-button>
        </div>
    @else
        <form wire:submit="subscribe" class="bg-white rounded-card shadow-card p-8 flex flex-col gap-6">
            <div class="newsletter-signup__email">
                <label for="newsletter-email">Je e-mailadres</label>
                <input
                    id="newsletter-email"
                    type="email"
                    wire:model.blur.live="email"
                    autocomplete="email"
                    inputmode="email"
                    maxlength="254"
                    spellcheck="false"
                    required
                    placeholder="jouw@email.be"
                    @error('email') aria-invalid="true" aria-describedby="newsletter-email-error" @enderror
                    class="newsletter-signup__input"
                >
                @error('email') <p id="newsletter-email-error" class="newsletter-signup__error" role="alert">{{ $message }}</p> @enderror
            </div>

            <x-cta-button
                variant="blue"
                icon="arrow"
                wire:click="subscribe"
                wire:target="subscribe"
                wire:loading.attr="disabled"
                class="self-start"
            >Schrijf me in</x-cta-button>

            <x-form-privacy-note>Je e-mailadres gebruiken we alleen voor de nieuwsbrief. Uitschrijven kan altijd met één klik.</x-form-privacy-note>
        </form>
    @endauth
</div>
