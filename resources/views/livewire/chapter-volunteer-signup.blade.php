<div>
    @if ($submitted)
        <div class="volunteer-signup__success space-y-3">
            <flux:icon.check-circle variant="solid" class="volunteer-signup__success-icon" aria-hidden="true" />
            <h4>Je bent erbij{{ $confirmedName ? ', '.$confirmedName : '' }}!</h4>
            <p>Iemand van het team van {{ $group->name }} neemt binnenkort contact op.</p>

            @if ($this->nextActivity)
                <p>Wacht niet op de mail. Kom alvast langs op onze volgende rit:</p>
                <x-event-card :activity="$this->nextActivity" class="max-w-sm" />
            @endif
        </div>
    @else
        <form wire:submit="submit" class="volunteer-signup__form space-y-4">
            {{-- Honeypot --}}
            <input type="text" wire:model="website" name="website" style="display:none" tabindex="-1" autocomplete="off">

            <div class="volunteer-signup__field">
                <label for="chapter-volunteer-name" class="volunteer-signup__label">Naam <span aria-hidden="true">*</span></label>
                <input type="text"
                       id="chapter-volunteer-name"
                       wire:model="name"
                       class="volunteer-signup__input"
                       autocomplete="name"
                       required
                       placeholder="Jouw naam">
                @error('name')
                    <span class="volunteer-signup__error">{{ $message }}</span>
                @enderror
            </div>

            <div class="volunteer-signup__field">
                <label for="chapter-volunteer-email" class="volunteer-signup__label">E-mailadres <span aria-hidden="true">*</span></label>
                <input type="email"
                       id="chapter-volunteer-email"
                       wire:model="email"
                       class="volunteer-signup__input"
                       autocomplete="email"
                       required
                       placeholder="jij@voorbeeld.be">
                @error('email')
                    <span class="volunteer-signup__error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Role interest — optional; "Nog niet zeker" removes the need-to-know-your-role barrier --}}
            <fieldset class="volunteer-signup__field">
                <legend class="volunteer-signup__label">Waarmee wil je helpen? <small>(optioneel)</small></legend>
                <div class="grid gap-1 sm:grid-cols-2">
                    @foreach ($roleOptions as $value => $label)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="roles" value="{{ $value }}">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="volunteer-signup__field">
                <label for="chapter-volunteer-message" class="volunteer-signup__label">Iets toe te voegen? <small>(optioneel)</small></label>
                <textarea id="chapter-volunteer-message"
                          wire:model="message"
                          class="volunteer-signup__input"
                          rows="3"
                          placeholder="Een vraag of een woordje uitleg"></textarea>
                @error('message')
                    <span class="volunteer-signup__error">{{ $message }}</span>
                @enderror
            </div>

            <flux:button type="submit" variant="primary" icon-trailing="arrow-right" wire:loading.attr="disabled" wire:target="submit">
                Ik doe mee
            </flux:button>
        </form>
    @endif
</div>
