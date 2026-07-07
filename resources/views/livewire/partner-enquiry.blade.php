<div>
    @if ($submitted)
        <div class="volunteer-signup__success space-y-3" role="status">
            <flux:icon.check-circle variant="solid" class="volunteer-signup__success-icon" aria-hidden="true" />
            <h4>Bedankt{{ $confirmedName ? ', '.$confirmedName : '' }}!</h4>
            <p>We hebben je aanvraag goed ontvangen. Iemand van het team neemt binnenkort contact op om samen de juiste formule te vinden.</p>
        </div>
    @else
        <form wire:submit="submit" class="volunteer-signup__form space-y-4">
            {{-- Honeypot --}}
            <input type="text" wire:model="website" name="website" style="display:none" tabindex="-1" autocomplete="off">

            <flux:field>
                <flux:label for="partner-name">Naam <span aria-hidden="true">*</span></flux:label>
                <flux:input type="text" id="partner-name" wire:model="name" autocomplete="name" required placeholder="Jouw naam" aria-describedby="partner-name-error" />
                <flux:error name="name" id="partner-name-error" />
            </flux:field>

            <flux:field>
                <flux:label for="partner-email">E-mailadres <span aria-hidden="true">*</span></flux:label>
                <flux:input type="email" id="partner-email" wire:model="email" autocomplete="email" required placeholder="jij@organisatie.be" aria-describedby="partner-email-error" />
                <flux:error name="email" id="partner-email-error" />
            </flux:field>

            <flux:field>
                <flux:label for="partner-organisation">Organisatie <span aria-hidden="true">*</span></flux:label>
                <flux:input type="text" id="partner-organisation" wire:model="organisation" autocomplete="organization" required placeholder="Naam van je vzw, bedrijf of gemeente" aria-describedby="partner-organisation-error" />
                <flux:error name="organisation" id="partner-organisation-error" />
            </flux:field>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label for="partner-type">Type organisatie <span aria-hidden="true">*</span></flux:label>
                    <flux:select id="partner-type" wire:model="type" required aria-describedby="partner-type-error">
                        <option value="">Maak een keuze</option>
                        @foreach ($typeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="type" id="partner-type-error" />
                </flux:field>

                <flux:field>
                    <flux:label for="partner-formule">Interesse in formule <small>(optioneel)</small></flux:label>
                    <flux:select id="partner-formule" wire:model="formule" aria-describedby="partner-formule-error">
                        <option value="">Nog niet zeker</option>
                        @foreach ($formuleOptions as $value => $label)
                            @if ($value !== 'nog-niet-zeker')
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </flux:select>
                    <flux:error name="formule" id="partner-formule-error" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label for="partner-message">Iets toe te voegen? <small>(optioneel)</small></flux:label>
                <flux:textarea id="partner-message" wire:model="message" rows="3" placeholder="Waarom wil je graag samenwerken, of een vraag" aria-describedby="partner-message-error" />
                <flux:error name="message" id="partner-message-error" />
            </flux:field>

            <x-cta-button type="submit" variant="blue" wire:loading.attr="disabled" wire:target="submit">
                Verstuur je aanvraag
            </x-cta-button>

            {{-- Always-rendered live region; the text only appears while submitting. --}}
            <p class="sr-only" role="status">
                <span wire:loading wire:target="submit">Bezig met versturen…</span>
            </p>

            <x-form-privacy-note>We gebruiken je gegevens alleen om je voorstel te beantwoorden.</x-form-privacy-note>
        </form>
    @endif
</div>
