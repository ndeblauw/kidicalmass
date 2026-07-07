<x-layouts::auth :title="__('auth.login_title')" :intro="__('auth.login_intro')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
        @csrf

        <div class="volunteer-signup__field">
            <label for="login-email" class="volunteer-signup__label">{{ __('auth.email') }}</label>
            <input type="email" id="login-email" name="email" value="{{ old('email') }}" class="volunteer-signup__input" required autofocus autocomplete="email" placeholder="naam@voorbeeld.be" @error('email') aria-invalid="true" aria-describedby="login-email-error" @enderror>
            @error('email')<span class="volunteer-signup__error" id="login-email-error" role="alert">{{ $message }}</span>@enderror
        </div>

        <div class="volunteer-signup__field">
            <div class="flex items-baseline justify-between">
                <label for="login-password" class="volunteer-signup__label">{{ __('auth.password_label') }}</label>
                @if (Route::has('password.request'))
                    <a class="text-sm" href="{{ route('password.request') }}">{{ __('auth.forgot_password') }}</a>
                @endif
            </div>
            <input type="password" id="login-password" name="password" class="volunteer-signup__input" required autocomplete="current-password" @error('password') aria-invalid="true" aria-describedby="login-password-error" @enderror>
            @error('password')<span class="volunteer-signup__error" id="login-password-error" role="alert">{{ $message }}</span>@enderror
        </div>

        <flux:checkbox name="remember" :label="__('auth.remember')" :checked="old('remember')" />

        <x-cta-button type="submit" variant="yellow" class="self-start" data-test="login-button">
            {{ __('auth.login_button') }}
        </x-cta-button>
    </form>

    @unless (app()->isProduction())
        <div class="auth-page__dev">
            <p class="auth-page__dev-label">{{ __('auth.dev_login_label') }}</p>
            <div class="grid grid-cols-2 gap-2">
                <x-cta-button :href="route('login.as', 'pinkvest')" variant="secondary" size="sm" block data-test="login-as-pinkvest">
                    {{ __('auth.dev_login_pinkvest') }}
                </x-cta-button>
                <x-cta-button :href="route('login.as', 'captain')" variant="secondary" size="sm" block data-test="login-as-captain">
                    {{ __('auth.dev_login_captain') }}
                </x-cta-button>
                <x-cta-button :href="route('login.as', 'user')" variant="secondary" size="sm" block data-test="login-as-user">
                    {{ __('auth.dev_login_user') }}
                </x-cta-button>
                <x-cta-button :href="route('login.as', 'admin')" variant="secondary" size="sm" block data-test="login-as-admin">
                    {{ __('auth.dev_login_admin') }}
                </x-cta-button>
            </div>
        </div>
    @endunless
</x-layouts::auth>
