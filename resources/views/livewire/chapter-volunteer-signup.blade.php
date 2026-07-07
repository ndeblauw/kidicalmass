<div>
    @if ($submitted)
        <div class="volunteer-signup__success space-y-3" role="status">
            <flux:icon.check-circle variant="solid" class="volunteer-signup__success-icon" aria-hidden="true" />
            <h4>Je bent erbij{{ $confirmedName ? ', '.$confirmedName : '' }}!</h4>
            <p>Iemand van het team van {{ $group->name }} neemt binnenkort contact op.</p>

            @if ($this->nextActivity)
                <p>Wacht niet op de mail. Kom alvast langs op onze volgende rit:</p>
                <div class="volunteer-signup__next-ride">
                    <x-ride-row :activity="$this->nextActivity" :show-date="true" />
                </div>
            @endif
        </div>
    @else
        <form wire:submit="submit" class="volunteer-signup__form space-y-4">
            {{-- Honeypot --}}
            <input type="text" wire:model="website" name="website" style="display:none" tabindex="-1" autocomplete="off">

            <flux:field>
                <flux:label for="chapter-volunteer-name">Naam <span aria-hidden="true">*</span></flux:label>
                <flux:input type="text" id="chapter-volunteer-name" wire:model="name" autocomplete="name" required placeholder="Jouw naam" aria-describedby="chapter-volunteer-name-error" />
                <flux:error name="name" id="chapter-volunteer-name-error" />
            </flux:field>

            <flux:field>
                <flux:label for="chapter-volunteer-email">E-mailadres <span aria-hidden="true">*</span></flux:label>
                <flux:input type="email" id="chapter-volunteer-email" wire:model="email" autocomplete="email" required placeholder="jij@voorbeeld.be" aria-describedby="chapter-volunteer-email-error" />
                <flux:error name="email" id="chapter-volunteer-email-error" />
            </flux:field>

            {{-- Role interest — optional; each role carries a one-line description so
                 people can pick what fits. "Nog niet zeker" removes the need-to-know
                 barrier. --}}
            <fieldset class="flex flex-col gap-1.5">
                <legend class="form-legend">Waarmee wil je helpen? <small>(optioneel)</small></legend>
                <div class="volunteer-roles">
                    @foreach ($roleOptions as $value => $label)
                        <label class="volunteer-role">
                            <input type="checkbox" class="volunteer-role__check" wire:model="roles" value="{{ $value }}">
                            <span class="volunteer-role__text">
                                <span class="volunteer-role__name">{{ $label }}</span>
                                <span class="volunteer-role__desc">{{ $roleDescriptions[$value] ?? '' }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <flux:field>
                <flux:label for="chapter-volunteer-message">Iets toe te voegen? <small>(optioneel)</small></flux:label>
                <flux:textarea id="chapter-volunteer-message" wire:model="message" rows="3" placeholder="Een vraag of een woordje uitleg" aria-describedby="chapter-volunteer-message-error" />
                <flux:error name="message" id="chapter-volunteer-message-error" />
            </flux:field>

            <x-cta-button variant="blue" icon="arrow" block
                          wire:click="submit"
                          wire:loading.attr="disabled"
                          wire:target="submit">
                Ik doe mee
            </x-cta-button>

            {{-- Always-rendered live region; the text only appears while submitting. --}}
            <p class="sr-only" role="status">
                <span wire:loading wire:target="submit">Bezig met versturen…</span>
            </p>

            <x-form-privacy-note>We gebruiken je gegevens alleen om je aanmelding op te volgen.</x-form-privacy-note>
        </form>
    @endif
</div>
