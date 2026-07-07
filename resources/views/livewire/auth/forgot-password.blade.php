<x-layouts::auth :title="__('auth.forgot_title')" :intro="__('auth.forgot_intro')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
        @csrf

        <div class="volunteer-signup__field">
            <label for="forgot-email" class="volunteer-signup__label">{{ __('auth.email') }}</label>
            <input type="email" id="forgot-email" name="email" class="volunteer-signup__input" required autofocus autocomplete="email" placeholder="naam@voorbeeld.be" @error('email') aria-invalid="true" aria-describedby="forgot-email-error" @enderror>
            @error('email')<span class="volunteer-signup__error" id="forgot-email-error" role="alert">{{ $message }}</span>@enderror
        </div>

        <x-cta-button type="submit" variant="yellow" class="self-start" data-test="email-password-reset-link-button">
            {{ __('auth.forgot_button') }}
        </x-cta-button>
    </form>

    <p class="mt-6 text-center text-sm">
        <flux:link :href="route('login')" wire:navigate>{{ __('auth.back_to_login') }}</flux:link>
    </p>
</x-layouts::auth>
