<div>
    @if ($submitted)
        <div class="volunteer-signup__success space-y-3" role="status">
            <flux:icon.check-circle variant="solid" class="volunteer-signup__success-icon" aria-hidden="true" />
            <h4>{!! __('forms.start_group.success_title', ['name' => $confirmedName ? ', '.$confirmedName : '']) !!}</h4>
            @if ($confirmedPath === 'praten')
                <p>{{ __('forms.start_group.success_praten') }}</p>
            @else
                <p>{{ __('forms.start_group.success_contact') }}</p>
            @endif
        </div>
    @else
        <form wire:submit="submit" class="volunteer-signup__form space-y-4">
            {{-- Honeypot --}}
            <input type="text" wire:model="website" name="website" style="display:none" tabindex="-1" autocomplete="off">

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label for="sg-name">{!! __('forms.start_group.name') !!} <span aria-hidden="true">*</span></flux:label>
                    <flux:input type="text" id="sg-name" wire:model="name" autocomplete="name" required :placeholder="__('forms.start_group.name_placeholder')" aria-describedby="sg-name-error" />
                    <flux:error name="name" id="sg-name-error" />
                </flux:field>

                <flux:field>
                    <flux:label for="sg-email">{!! __('forms.start_group.email') !!} <span aria-hidden="true">*</span></flux:label>
                    <flux:input type="email" id="sg-email" wire:model="email" autocomplete="email" required :placeholder="__('forms.start_group.email_placeholder')" aria-describedby="sg-email-error" />
                    <flux:error name="email" id="sg-email-error" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label for="sg-place">{!! __('forms.start_group.place') !!} <span aria-hidden="true">*</span></flux:label>
                <flux:input type="text" id="sg-place" wire:model="place" autocomplete="address-level2" required :placeholder="__('forms.start_group.place_placeholder')" aria-describedby="sg-place-error" />
                <flux:error name="place" id="sg-place-error" />
            </flux:field>

            <flux:field>
                <flux:label for="sg-motivation">{!! __('forms.start_group.motivation') !!} <span aria-hidden="true">*</span></flux:label>
                <flux:textarea id="sg-motivation" wire:model="motivation" rows="3" required :placeholder="__('forms.start_group.motivation_placeholder')" aria-describedby="sg-motivation-error" />
                <flux:error name="motivation" id="sg-motivation-error" />
            </flux:field>

            <flux:field>
                <flux:label for="sg-team">{!! __('forms.start_group.team') !!}</flux:label>
                <flux:select id="sg-team" wire:model="team" aria-describedby="sg-team-error">
                    <option value="">{{ __('forms.start_group.team_placeholder') }}</option>
                    @foreach ($teamOptions as $value => $label)
                        <option value="{{ $value }}">{{ __($label) }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="team" id="sg-team-error" />
            </flux:field>

            <fieldset class="sg-form__choice" @error('path') aria-describedby="sg-path-error" @enderror>
                <legend class="form-legend">{!! __('forms.start_group.path_legend') !!} <span aria-hidden="true">*</span></legend>
                @foreach ($pathOptions as $value => $label)
                    <label class="sg-form__option">
                        <input type="radio" wire:model="path" name="path" value="{{ $value }}" required>
                        <span>{{ __($label) }}</span>
                    </label>
                @endforeach
                <flux:error name="path" id="sg-path-error" />
            </fieldset>

            <x-cta-button variant="blue" icon="arrow" block
                          wire:click="submit"
                          wire:loading.attr="disabled"
                          wire:target="submit">
                {{ __('forms.start_group.submit') }}
            </x-cta-button>

            {{-- Always-rendered live region; the text only appears while submitting. --}}
            <p class="sr-only" role="status">
                <span wire:loading wire:target="submit">{{ __('forms.start_group.sending') }}</span>
            </p>

            <x-form-privacy-note>{{ __('forms.start_group.privacy') }}</x-form-privacy-note>

            <p class="sg-form__reassure">{{ __('forms.start_group.reassurance') }}</p>
        </form>
    @endif
</div>
