{{-- National contact form (public style, mirrors partner-enquiry). Field styling
     comes from the restyled Flux components (components/form-field.css). --}}
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
                <legend class="form-legend">Waarover gaat het? <small>(optioneel)</small></legend>
                <div class="contact-topic-pills">
                    @foreach (\App\Livewire\ContactFormComponent::TOPICS as $value => $label)
                        <label class="contact-topic-pill">
                            <input type="radio" wire:model="topic" name="topic" value="{{ $value }}" class="sr-only">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <flux:field>
                <flux:label for="contact-name">Naam <span aria-hidden="true">*</span></flux:label>
                <flux:input type="text" id="contact-name" wire:model="name" autocomplete="name" required placeholder="Jouw naam" aria-describedby="contact-name-error" />
                <flux:error name="name" id="contact-name-error" />
            </flux:field>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label for="contact-email">E-mailadres <span aria-hidden="true">*</span></flux:label>
                    <flux:input type="email" id="contact-email" wire:model="email" autocomplete="email" required placeholder="jij@voorbeeld.be" aria-describedby="contact-email-error" />
                    <flux:error name="email" id="contact-email-error" />
                </flux:field>

                <flux:field>
                    <flux:label for="contact-phone">Telefoon <small>(optioneel)</small></flux:label>
                    <flux:input type="tel" id="contact-phone" wire:model="phone" autocomplete="tel" aria-describedby="contact-phone-error" />
                    <flux:error name="phone" id="contact-phone-error" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label for="contact-message">Je bericht <span aria-hidden="true">*</span></flux:label>
                <flux:textarea id="contact-message" wire:model="message" rows="6" required placeholder="Waarmee kunnen we je helpen?" aria-describedby="contact-message-error" />
                <flux:error name="message" id="contact-message-error" />
            </flux:field>

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
