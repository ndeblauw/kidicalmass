{{-- National contact form (public style, mirrors partner-enquiry). Field styling
     comes from the shared .volunteer-signup__* vocabulary (pages/steun.css). --}}
<div>
    @if ($submitted)
        <div class="volunteer-signup__success space-y-3" role="status">
            <flux:icon.check-circle variant="solid" class="volunteer-signup__success-icon" aria-hidden="true" />
            <h3>Bedankt voor je bericht!</h3>
            <p>Het is goed aangekomen. Iemand van het coördinatieduo antwoordt je zo snel mogelijk.</p>
        </div>
    @else
        <form wire:submit="submit" class="volunteer-signup__form">
            {{-- Honeypot --}}
            <input type="text" wire:model="website" name="website" style="display:none" tabindex="-1" autocomplete="off">

            {{-- A <legend> sits outside the field's flex flow, so the label-to-pills
                 gap is set in .contact-topic-fieldset, not by the flex gap. --}}
            <fieldset class="contact-topic-fieldset">
                <legend class="volunteer-signup__label">Waarover gaat het? <small>(optioneel)</small></legend>
                <div class="contact-topic-pills">
                    @foreach (\App\Livewire\ContactFormComponent::TOPICS as $value => $label)
                        <label class="contact-topic-pill">
                            <input type="radio" wire:model="topic" name="topic" value="{{ $value }}" class="sr-only">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="volunteer-signup__field">
                <label for="contact-name" class="volunteer-signup__label">Naam <span aria-hidden="true">*</span></label>
                <input type="text" id="contact-name" wire:model="name" class="volunteer-signup__input" autocomplete="name" required placeholder="Jouw naam" @error('name') aria-invalid="true" aria-describedby="contact-name-error" @enderror>
                @error('name')<span class="volunteer-signup__error" id="contact-name-error" role="alert">{{ $message }}</span>@enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="volunteer-signup__field">
                    <label for="contact-email" class="volunteer-signup__label">E-mailadres <span aria-hidden="true">*</span></label>
                    <input type="email" id="contact-email" wire:model="email" class="volunteer-signup__input" autocomplete="email" required placeholder="jij@voorbeeld.be" @error('email') aria-invalid="true" aria-describedby="contact-email-error" @enderror>
                    @error('email')<span class="volunteer-signup__error" id="contact-email-error" role="alert">{{ $message }}</span>@enderror
                </div>

                <div class="volunteer-signup__field">
                    <label for="contact-phone" class="volunteer-signup__label">Telefoon <small>(optioneel)</small></label>
                    <input type="tel" id="contact-phone" wire:model="phone" class="volunteer-signup__input" autocomplete="tel" @error('phone') aria-invalid="true" aria-describedby="contact-phone-error" @enderror>
                    @error('phone')<span class="volunteer-signup__error" id="contact-phone-error" role="alert">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="volunteer-signup__field">
                <label for="contact-message" class="volunteer-signup__label">Je bericht <span aria-hidden="true">*</span></label>
                <textarea id="contact-message" wire:model="message" class="volunteer-signup__input" rows="6" required placeholder="Waarmee kunnen we je helpen?" @error('message') aria-invalid="true" aria-describedby="contact-message-error" @enderror></textarea>
                @error('message')<span class="volunteer-signup__error" id="contact-message-error" role="alert">{{ $message }}</span>@enderror
            </div>

            <x-cta-button type="submit" variant="blue" wire:loading.attr="disabled" wire:target="submit">
                Verstuur je bericht
            </x-cta-button>

            {{-- Always-rendered live region; the text only appears while submitting. --}}
            <p class="sr-only" role="status">
                <span wire:loading wire:target="submit">Bezig met versturen…</span>
            </p>

            <x-form-privacy-note>We gebruiken je gegevens alleen om je bericht te beantwoorden.</x-form-privacy-note>
        </form>
    @endif
</div>
