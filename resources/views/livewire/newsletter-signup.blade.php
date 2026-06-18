<div>
    @auth
        <div class="bg-kidical-light-blue rounded-card p-8 flex flex-col gap-4 items-start">
            <h2 class="text-kidical-ink">Je bent al mee</h2>
            <p class="text-kidical-ink/75">Je staat op de hoogte. Je nieuwsvoorkeuren beheer je in je profiel.</p>
            <x-cta-button variant="blue" :href="route('settings')">Beheer voorkeuren</x-cta-button>
        </div>
    @elseif ($submitted)
        <div class="bg-white rounded-card shadow-card p-8 flex flex-col gap-4 items-start">
            <h2>Kijk even in je mailbox</h2>
            <p>We stuurden een mailtje naar <strong>{{ $email }}</strong>. Klik op de link erin om je inschrijving te bevestigen.</p>
            <p class="text-kidical-ink/70">Niets ontvangen? Check je spam.</p>
        </div>
    @else
        {{-- Email first: the essential input. Group/location selection is optional
             refinement, revealed on demand. The reactive picker dispatches
             location-selected instead of navigating, so this email survives. --}}
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
                    placeholder="jouw@email.be"
                    @error('email') aria-invalid="true" aria-describedby="newsletter-email-error" @enderror
                    class="newsletter-signup__input"
                >
                @error('email') <p id="newsletter-email-error" class="newsletter-signup__error" role="alert">{{ $message }}</p> @enderror
            </div>

            <div class="newsletter-signup__groups">
                @if ($showGroups)
                    <div class="newsletter-signup__reveal newsletter-signup__location">
                        <livewire:location-picker :reactive="true" :compact="true" wire:key="newsletter-location" />
                    </div>

                    @if ($groups->isNotEmpty())
                        <div class="newsletter-signup__reveal" role="group" aria-labelledby="newsletter-groups-hint">
                            <p id="newsletter-groups-hint" class="newsletter-signup__groups-hint">
                                We sturen je standaard de ritten van deze groepen. Klik een groep weg die je niet wil volgen.
                            </p>
                            {{-- The chips are inert while "Heel België" is ticked: the
                                 per-group picks no longer apply, handled in CSS via :has(). --}}
                            <div class="newsletter-signup__chips">
                                @foreach ($groups as $group)
                                    <label class="newsletter-signup__chip">
                                        <input type="checkbox" wire:model="selectedGroups" value="{{ $group->id }}" class="sr-only">
                                        <span>{{ $group->gemeente }}</span>
                                    </label>
                                @endforeach

                                {{-- Only offer the countrywide opt-in when there are chapters
                                     the nearby chips don't already cover. Ticking it confirms
                                     itself with a checkmark, like any other chip. --}}
                                @if ($totalGroups > $groups->count())
                                    <label class="newsletter-signup__chip newsletter-signup__chip--all">
                                        <input type="checkbox" wire:model="followAll" class="sr-only">
                                        <span>Heel België</span>
                                    </label>
                                @endif
                            </div>
                            @error('selectedGroups') <p class="newsletter-signup__error" role="alert">{{ $message }}</p> @enderror
                        </div>
                    @endif
                @else
                    <button type="button" wire:click="revealGroups" class="newsletter-signup__disclosure">
                        Ritten bij jou in de buurt kiezen
                    </button>
                @endif
            </div>

            {{-- cta-button hardcodes type="button", so it cannot natively submit a
                 form; wire:click handles the click and the form's wire:submit
                 handles Enter. Both route to subscribe(). self-start keeps it
                 sized to its label instead of stretching to the column. --}}
            {{-- Guard against double-submit: disable while the subscribe round-trip
                 is in flight so an impatient double-click can't fire it twice. --}}
            <x-cta-button
                variant="blue"
                icon="arrow"
                wire:click="subscribe"
                wire:target="subscribe"
                wire:loading.attr="disabled"
                class="self-start"
            >Schrijf me in</x-cta-button>
        </form>
    @endauth
</div>
