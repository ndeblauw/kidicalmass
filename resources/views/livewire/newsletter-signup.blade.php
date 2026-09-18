<div>
    @auth
        <div class="bg-kidical-light-blue rounded-card p-8 flex flex-col gap-4 items-start">
            <h2 class="text-kidical-ink">{{ __('forms.newsletter.auth_heading') }}</h2>
            <p class="text-kidical-ink/75">{{ __('forms.newsletter.auth_body') }}</p>
            <x-cta-button variant="blue" :href="route('settings')">{{ __('forms.newsletter.auth_button') }}</x-cta-button>
        </div>
    @elseif ($submitted)
        <div class="bg-white rounded-card shadow-card p-8 flex flex-col gap-4 items-start" role="status">
            <h2>{{ __('forms.newsletter.submitted_heading') }}</h2>
            <p>{!! __('forms.newsletter.submitted_body', ['email' => '<strong>'.e($email).'</strong>']) !!}</p>
            <p class="text-kidical-ink/70">{{ __('forms.newsletter.submitted_hint') }}</p>
        </div>
    @else
        <form wire:submit="subscribe" class="bg-white rounded-card shadow-card p-8 flex flex-col gap-6">
            <div class="newsletter-signup__email">
                <label for="newsletter-email">{{ __('forms.newsletter.email_label') }}</label>
                <input
                    id="newsletter-email"
                    type="email"
                    wire:model.blur.live="email"
                    autocomplete="email"
                    inputmode="email"
                    maxlength="254"
                    spellcheck="false"
                    required
                    :placeholder="__('forms.newsletter.email_placeholder')"
                    @error('email') aria-invalid="true" aria-describedby="newsletter-email-error" @enderror
                    class="newsletter-signup__input"
                >
                @error('email') <p id="newsletter-email-error" class="newsletter-signup__error" role="alert">{{ $message }}</p> @enderror
            </div>

            <x-cta-button
                variant="blue"
                icon="arrow"
                wire:click="subscribe"
                wire:target="subscribe"
                wire:loading.attr="disabled"
                class="self-start"
            >{{ __('forms.newsletter.submit') }}</x-cta-button>

            <x-form-privacy-note>{{ __('forms.newsletter.privacy') }}</x-form-privacy-note>
        </form>
    @endauth
</div>
