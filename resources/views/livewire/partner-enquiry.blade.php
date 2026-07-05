<div>
    @if ($submitted)
        <div class="volunteer-signup__success space-y-3">
            <flux:icon.check-circle variant="solid" class="volunteer-signup__success-icon" aria-hidden="true" />
            <h4>Bedankt{{ $confirmedName ? ', '.$confirmedName : '' }}!</h4>
            <p>We hebben je aanvraag goed ontvangen. Iemand van het team neemt binnenkort contact op om samen de juiste formule te vinden.</p>
        </div>
    @else
        <form wire:submit="submit" class="volunteer-signup__form space-y-4">
            {{-- Honeypot --}}
            <input type="text" wire:model="website" name="website" style="display:none" tabindex="-1" autocomplete="off">

            <div class="volunteer-signup__field">
                <label for="partner-name" class="volunteer-signup__label">Naam <span aria-hidden="true">*</span></label>
                <input type="text" id="partner-name" wire:model="name" class="volunteer-signup__input" autocomplete="name" required placeholder="Jouw naam">
                @error('name')<span class="volunteer-signup__error">{{ $message }}</span>@enderror
            </div>

            <div class="volunteer-signup__field">
                <label for="partner-email" class="volunteer-signup__label">E-mailadres <span aria-hidden="true">*</span></label>
                <input type="email" id="partner-email" wire:model="email" class="volunteer-signup__input" autocomplete="email" required placeholder="jij@organisatie.be">
                @error('email')<span class="volunteer-signup__error">{{ $message }}</span>@enderror
            </div>

            <div class="volunteer-signup__field">
                <label for="partner-organisation" class="volunteer-signup__label">Organisatie <span aria-hidden="true">*</span></label>
                <input type="text" id="partner-organisation" wire:model="organisation" class="volunteer-signup__input" autocomplete="organization" required placeholder="Naam van je vzw, bedrijf of gemeente">
                @error('organisation')<span class="volunteer-signup__error">{{ $message }}</span>@enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="volunteer-signup__field">
                    <label for="partner-type" class="volunteer-signup__label">Type organisatie <span aria-hidden="true">*</span></label>
                    <select id="partner-type" wire:model="type" class="volunteer-signup__input" required>
                        <option value="">Maak een keuze</option>
                        @foreach ($typeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')<span class="volunteer-signup__error">{{ $message }}</span>@enderror
                </div>

                <div class="volunteer-signup__field">
                    <label for="partner-formule" class="volunteer-signup__label">Interesse in formule <small>(optioneel)</small></label>
                    <select id="partner-formule" wire:model="formule" class="volunteer-signup__input">
                        <option value="">Nog niet zeker</option>
                        @foreach ($formuleOptions as $value => $label)
                            @if ($value !== 'nog-niet-zeker')
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('formule')<span class="volunteer-signup__error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="volunteer-signup__field">
                <label for="partner-message" class="volunteer-signup__label">Iets toe te voegen? <small>(optioneel)</small></label>
                <textarea id="partner-message" wire:model="message" class="volunteer-signup__input" rows="3" placeholder="Waarom wil je graag samenwerken, of een vraag"></textarea>
                @error('message')<span class="volunteer-signup__error">{{ $message }}</span>@enderror
            </div>

            <x-cta-button type="submit" variant="blue" wire:loading.attr="disabled" wire:target="submit">
                Verstuur je aanvraag
            </x-cta-button>

            <x-form-privacy-note>We gebruiken je gegevens alleen om je voorstel te beantwoorden.</x-form-privacy-note>
        </form>
    @endif
</div>
