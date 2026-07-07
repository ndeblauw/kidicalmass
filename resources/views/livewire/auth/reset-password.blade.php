<x-layouts::auth :title="__('auth.reset_title')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5">
        @csrf
        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <div class="volunteer-signup__field">
            <label for="reset-email" class="volunteer-signup__label">{{ __('auth.email') }}</label>
            <input type="email" id="reset-email" name="email" value="{{ request('email') }}" class="volunteer-signup__input" required autocomplete="email" @error('email') aria-invalid="true" aria-describedby="reset-email-error" @enderror>
            @error('email')<span class="volunteer-signup__error" id="reset-email-error" role="alert">{{ $message }}</span>@enderror
        </div>

        <div class="volunteer-signup__field">
            <label for="reset-password" class="volunteer-signup__label">{{ __('auth.password_label') }}</label>
            <input type="password" id="reset-password" name="password" class="volunteer-signup__input" required autocomplete="new-password" @error('password') aria-invalid="true" aria-describedby="reset-password-error" @enderror>
            @error('password')<span class="volunteer-signup__error" id="reset-password-error" role="alert">{{ $message }}</span>@enderror
        </div>

        <div class="volunteer-signup__field">
            <label for="reset-password-confirmation" class="volunteer-signup__label">{{ __('auth.password_confirm_label') }}</label>
            <input type="password" id="reset-password-confirmation" name="password_confirmation" class="volunteer-signup__input" required autocomplete="new-password">
        </div>

        <x-cta-button type="submit" variant="yellow" class="self-start" data-test="reset-password-button">
            {{ __('auth.reset_button') }}
        </x-cta-button>
    </form>
</x-layouts::auth>
