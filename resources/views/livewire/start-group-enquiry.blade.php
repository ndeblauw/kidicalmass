<div>
    @if ($submitted)
        <div class="volunteer-signup__success space-y-3">
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
                <div class="volunteer-signup__field">
                    <label for="sg-name" class="volunteer-signup__label">Naam <span aria-hidden="true">*</span></label>
                    <input type="text" id="sg-name" wire:model="name" class="volunteer-signup__input" autocomplete="name" required placeholder="Jouw naam">
                    @error('name')<span class="volunteer-signup__error">{{ $message }}</span>@enderror
                </div>

                <div class="volunteer-signup__field">
                    <label for="sg-email" class="volunteer-signup__label">E-mailadres <span aria-hidden="true">*</span></label>
                    <input type="email" id="sg-email" wire:model="email" class="volunteer-signup__input" autocomplete="email" required placeholder="jij@voorbeeld.be">
                    @error('email')<span class="volunteer-signup__error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="volunteer-signup__field">
                <label for="sg-place" class="volunteer-signup__label">Gemeente of postcode <span aria-hidden="true">*</span></label>
                <input type="text" id="sg-place" wire:model="place" class="volunteer-signup__input" autocomplete="address-level2" required placeholder="Waar wil je starten?">
                @error('place')<span class="volunteer-signup__error">{{ $message }}</span>@enderror
            </div>

            <div class="volunteer-signup__field">
                <label for="sg-motivation" class="volunteer-signup__label">Wat trekt je aan? <span aria-hidden="true">*</span></label>
                <textarea id="sg-motivation" wire:model="motivation" class="volunteer-signup__input" rows="3" required placeholder="Eén of twee zinnen over waarom je dit in jouw buurt wil"></textarea>
                @error('motivation')<span class="volunteer-signup__error">{{ $message }}</span>@enderror
            </div>

            <div class="volunteer-signup__field">
                <label for="sg-team" class="volunteer-signup__label">Heb je al mensen die mee willen? <small>(optioneel)</small></label>
                <select id="sg-team" wire:model="team" class="volunteer-signup__input">
                    <option value="">Maak een keuze</option>
                    @foreach ($teamOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('team')<span class="volunteer-signup__error">{{ $message }}</span>@enderror
            </div>

            <fieldset class="volunteer-signup__field sg-form__choice">
                <legend class="volunteer-signup__label">Wat wil je nu? <span aria-hidden="true">*</span></legend>
                @foreach ($pathOptions as $value => $label)
                    <label class="sg-form__option">
                        <input type="radio" wire:model="path" name="path" value="{{ $value }}" required>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
                @error('path')<span class="volunteer-signup__error">{{ $message }}</span>@enderror
            </fieldset>

            <button type="submit" class="about-cta__btn about-cta__btn--primary" wire:loading.attr="disabled" wire:target="submit">
                Verstuur
                <span aria-hidden="true">→</span>
            </button>

            <p class="sg-form__reassure">Een echt mens leest dit en antwoordt je persoonlijk. Geen automatische mailbox.</p>
        </form>
    @endif
</div>
