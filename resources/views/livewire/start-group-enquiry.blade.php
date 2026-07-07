<div>
    @if ($submitted)
        <div class="volunteer-signup__success space-y-3" role="status">
            <flux:icon.check-circle variant="solid" class="volunteer-signup__success-icon" aria-hidden="true" />
            <h4>Bedankt{{ $confirmedName ? ', '.$confirmedName : '' }}!</h4>
            @if ($confirmedPath === 'praten')
                <p>We zoeken een trekker bij jou in de buurt die het al deed, en brengen jullie met elkaar in contact. Een echt mens leest je bericht, geen centrale mailbox.</p>
            @else
                <p>We hebben je bericht goed ontvangen. Iemand van het coördinatieteam neemt binnenkort contact op om samen de eerste stappen te zetten.</p>
            @endif
        </div>
    @else
        <form wire:submit="submit" class="volunteer-signup__form space-y-4">
            {{-- Honeypot --}}
            <input type="text" wire:model="website" name="website" style="display:none" tabindex="-1" autocomplete="off">

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label for="sg-name">Naam <span aria-hidden="true">*</span></flux:label>
                    <flux:input type="text" id="sg-name" wire:model="name" autocomplete="name" required placeholder="Jouw naam" aria-describedby="sg-name-error" />
                    <flux:error name="name" id="sg-name-error" />
                </flux:field>

                <flux:field>
                    <flux:label for="sg-email">E-mailadres <span aria-hidden="true">*</span></flux:label>
                    <flux:input type="email" id="sg-email" wire:model="email" autocomplete="email" required placeholder="jij@voorbeeld.be" aria-describedby="sg-email-error" />
                    <flux:error name="email" id="sg-email-error" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label for="sg-place">Gemeente of postcode <span aria-hidden="true">*</span></flux:label>
                <flux:input type="text" id="sg-place" wire:model="place" autocomplete="address-level2" required placeholder="Waar wil je starten?" aria-describedby="sg-place-error" />
                <flux:error name="place" id="sg-place-error" />
            </flux:field>

            <flux:field>
                <flux:label for="sg-motivation">Wat trekt je aan? <span aria-hidden="true">*</span></flux:label>
                <flux:textarea id="sg-motivation" wire:model="motivation" rows="3" required placeholder="Eén of twee zinnen over waarom je dit in jouw buurt wil" aria-describedby="sg-motivation-error" />
                <flux:error name="motivation" id="sg-motivation-error" />
            </flux:field>

            <flux:field>
                <flux:label for="sg-team">Heb je al mensen die mee willen? <small>(optioneel)</small></flux:label>
                <flux:select id="sg-team" wire:model="team" aria-describedby="sg-team-error">
                    <option value="">Maak een keuze</option>
                    @foreach ($teamOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="team" id="sg-team-error" />
            </flux:field>

            <fieldset class="sg-form__choice" @error('path') aria-describedby="sg-path-error" @enderror>
                <legend class="form-legend">Wat wil je nu? <span aria-hidden="true">*</span></legend>
                @foreach ($pathOptions as $value => $label)
                    <label class="sg-form__option">
                        <input type="radio" wire:model="path" name="path" value="{{ $value }}" required>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
                <flux:error name="path" id="sg-path-error" />
            </fieldset>

            <x-cta-button variant="blue" icon="arrow" block
                          wire:click="submit"
                          wire:loading.attr="disabled"
                          wire:target="submit">
                Verstuur
            </x-cta-button>

            {{-- Always-rendered live region; the text only appears while submitting. --}}
            <p class="sr-only" role="status">
                <span wire:loading wire:target="submit">Bezig met versturen…</span>
            </p>

            <x-form-privacy-note>We gebruiken je gegevens alleen om samen jouw groep op te starten.</x-form-privacy-note>

            <p class="sg-form__reassure">Een echt mens leest dit en antwoordt je persoonlijk. Geen automatische mailbox.</p>
        </form>
    @endif
</div>
