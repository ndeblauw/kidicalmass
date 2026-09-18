{{-- National contact form (public style, mirrors partner-enquiry). Field styling
     comes from the restyled Flux components (components/form-field.css). --}}
<div>
    @if ($submitted)
        <div class="volunteer-signup__success space-y-3" role="status">
            <flux:icon.check-circle variant="solid" class="volunteer-signup__success-icon" aria-hidden="true" />
            <h3>{{ __('forms.contact.success_title') }}</h3>
            <p>{{ __('forms.contact.success_body') }}</p>
        </div>
    @else
        <form wire:submit="submit" class="volunteer-signup__form">
            {{-- Honeypot --}}
            <input type="text" wire:model="website" name="website" style="display:none" tabindex="-1" autocomplete="off">

            {{-- A <legend> sits outside the field's flex flow, so the label-to-pills
                 gap is set in .contact-topic-fieldset, not by the flex gap. --}}
            <fieldset class="contact-topic-fieldset">
                <legend class="form-legend">{!! __('forms.contact.legend') !!}</legend>
                <div class="contact-topic-pills">
                    @foreach (\App\Livewire\ContactFormComponent::TOPICS as $value => $label)
                        <label class="contact-topic-pill">
                            <input type="radio" wire:model="topic" name="topic" value="{{ $value }}" class="sr-only">
                            <span>{{ __($label) }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <flux:field>
                <flux:label for="contact-name">{!! __('forms.contact.name') !!} <span aria-hidden="true">*</span></flux:label>
                <flux:input type="text" id="contact-name" wire:model="name" autocomplete="name" required :placeholder="__('forms.contact.name_placeholder')" aria-describedby="contact-name-error" />
                <flux:error name="name" id="contact-name-error" />
            </flux:field>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label for="contact-email">{!! __('forms.contact.email') !!} <span aria-hidden="true">*</span></flux:label>
                    <flux:input type="email" id="contact-email" wire:model="email" autocomplete="email" required :placeholder="__('forms.contact.email_placeholder')" aria-describedby="contact-email-error" />
                    <flux:error name="email" id="contact-email-error" />
                </flux:field>

                <flux:field>
                    <flux:label for="contact-phone">{!! __('forms.contact.phone') !!}</flux:label>
                    <flux:input type="tel" id="contact-phone" wire:model="phone" autocomplete="tel" aria-describedby="contact-phone-error" />
                    <flux:error name="phone" id="contact-phone-error" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label for="contact-message">{!! __('forms.contact.message') !!} <span aria-hidden="true">*</span></flux:label>
                <flux:textarea id="contact-message" wire:model="message" rows="6" required :placeholder="__('forms.contact.message_placeholder')" aria-describedby="contact-message-error" />
                <flux:error name="message" id="contact-message-error" />
            </flux:field>

            <x-cta-button type="submit" variant="blue" wire:loading.attr="disabled" wire:target="submit">
                {{ __('forms.contact.submit') }}
            </x-cta-button>

            {{-- Always-rendered live region; the text only appears while submitting. --}}
            <p class="sr-only" role="status">
                <span wire:loading wire:target="submit">{{ __('forms.contact.sending') }}</span>
            </p>

            <x-form-privacy-note>{{ __('forms.contact.privacy') }}</x-form-privacy-note>
        </form>
    @endif
</div>
