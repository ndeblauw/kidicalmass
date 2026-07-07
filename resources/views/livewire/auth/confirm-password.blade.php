<x-layouts::auth :title="__('auth.confirm_title')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-5">
        @csrf

        <div class="volunteer-signup__field">
            <label for="confirm-password" class="volunteer-signup__label">{{ __('auth.password_label') }}</label>
            <input type="password" id="confirm-password" name="password" class="volunteer-signup__input" required autocomplete="current-password" @error('password') aria-invalid="true" aria-describedby="confirm-password-error" @enderror>
            @error('password')<span class="volunteer-signup__error" id="confirm-password-error" role="alert">{{ $message }}</span>@enderror
        </div>

        <x-cta-button type="submit" variant="yellow" class="self-start" data-test="confirm-password-button">
            {{ __('auth.confirm_button') }}
        </x-cta-button>
    </form>
</x-layouts::auth>
