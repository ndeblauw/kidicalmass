<div>
    @if($submitted)
        <div class="volunteer-signup__success">
            <flux:icon.check-circle variant="solid" class="volunteer-signup__success-icon" aria-hidden="true" />
            <h3>Bedankt, {{ explode(' ', $name)[0] ?? 'je aanmelding' }}!</h3>
            <p>We nemen binnenkort contact op.</p>
        </div>
    @else
        <form wire:submit="submit" class="volunteer-signup__form">
            {{-- Honeypot --}}
            <input type="text" wire:model="website" name="website" style="display:none" tabindex="-1" autocomplete="off">

            <div class="volunteer-signup__field">
                <label for="volunteer-name" class="volunteer-signup__label">Naam <span aria-hidden="true">*</span></label>
                <input type="text"
                       id="volunteer-name"
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
                <label for="volunteer-email" class="volunteer-signup__label">E-mailadres <span aria-hidden="true">*</span></label>
                <input type="email"
                       id="volunteer-email"
                       wire:model="email"
                       class="volunteer-signup__input"
                       autocomplete="email"
                       required
                       placeholder="jij@voorbeeld.be">
                @error('email')
                    <span class="volunteer-signup__error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="activity-organizers__join-btn" wire:loading.attr="disabled">
                <span wire:loading.remove>Meld me aan</span>
                <span wire:loading>Bezig…</span>
            </button>
        </form>
    @endif
</div>
