<div>
    @if ($submitted)
        <div class="volunteer-signup__success space-y-3" role="status">
            <flux:icon.check-circle variant="solid" class="volunteer-signup__success-icon" aria-hidden="true" />
            <h4>{{ __('partners.page.enquiry.form.success_title') }}{{ $confirmedName ? ', '.$confirmedName : '' }}!</h4>
            <p>{{ __('partners.page.enquiry.form.success_body') }}</p>
        </div>
    @else
        <form wire:submit="submit" class="volunteer-signup__form space-y-4">
            {{-- Honeypot --}}
            <input type="text" wire:model="website" name="website" style="display:none" tabindex="-1" autocomplete="off">

            <flux:field>
                <flux:label for="partner-name">{{ __('partners.page.enquiry.form.name') }} <span aria-hidden="true">*</span></flux:label>
                <flux:input type="text" id="partner-name" wire:model="name" autocomplete="name" required placeholder="{{ __('partners.page.enquiry.form.name_placeholder') }}" aria-describedby="partner-name-error" />
                <flux:error name="name" id="partner-name-error" />
            </flux:field>

            <flux:field>
                <flux:label for="partner-email">{{ __('partners.page.enquiry.form.email') }} <span aria-hidden="true">*</span></flux:label>
                <flux:input type="email" id="partner-email" wire:model="email" autocomplete="email" required placeholder="{{ __('partners.page.enquiry.form.email_placeholder') }}" aria-describedby="partner-email-error" />
                <flux:error name="email" id="partner-email-error" />
            </flux:field>

            <flux:field>
                <flux:label for="partner-organisation">{{ __('partners.page.enquiry.form.organisation') }} <span aria-hidden="true">*</span></flux:label>
                <flux:input type="text" id="partner-organisation" wire:model="organisation" autocomplete="organization" required placeholder="{{ __('partners.page.enquiry.form.organisation_placeholder') }}" aria-describedby="partner-organisation-error" />
                <flux:error name="organisation" id="partner-organisation-error" />
            </flux:field>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label for="partner-type">{{ __('partners.page.enquiry.form.type') }} <span aria-hidden="true">*</span></flux:label>
                    <flux:select id="partner-type" wire:model="type" required aria-describedby="partner-type-error">
                        <option value="">{{ __('partners.page.enquiry.form.type_placeholder') }}</option>
                        @foreach ($typeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="type" id="partner-type-error" />
                </flux:field>

                <flux:field>
                    <flux:label for="partner-formule">{{ __('partners.page.enquiry.form.formule') }} <small>{{ __('partners.page.enquiry.form.formule_optional') }}</small></flux:label>
                    <flux:select id="partner-formule" wire:model="formule" aria-describedby="partner-formule-error">
                        <option value="">{{ __('partners.page.enquiry.formules.nog-niet-zeker') }}</option>
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
                <flux:label for="partner-message">{{ __('partners.page.enquiry.form.message') }} <small>{{ __('partners.page.enquiry.form.formule_optional') }}</small></flux:label>
                <flux:textarea id="partner-message" wire:model="message" rows="3" placeholder="{{ __('partners.page.enquiry.form.message_placeholder') }}" aria-describedby="partner-message-error" />
                <flux:error name="message" id="partner-message-error" />
            </flux:field>

            <x-cta-button type="submit" variant="blue" wire:loading.attr="disabled" wire:target="submit">
                {{ __('partners.page.enquiry.form.submit') }}
            </x-cta-button>

            {{-- Always-rendered live region; the text only appears while submitting. --}}
            <p class="sr-only" role="status">
                <span wire:loading wire:target="submit">{{ __('partners.page.enquiry.form.sending') }}</span>
            </p>

            <x-form-privacy-note>{{ __('partners.page.enquiry.form.privacy') }}</x-form-privacy-note>
        </form>
    @endif
</div>