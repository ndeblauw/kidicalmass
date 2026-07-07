<x-layouts::auth :title="__('auth.login_title')" :intro="__('auth.login_intro')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
        @csrf

        <flux:input
            name="email"
            :label="__('auth.email')"
            :value="old('email')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="naam@voorbeeld.be"
        />

        <div class="relative">
            <flux:input
                name="password"
                :label="__('auth.password_label')"
                type="password"
                required
                autocomplete="current-password"
                viewable
            />

            @if (Route::has('password.request'))
                <flux:link class="absolute top-0 end-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('auth.forgot_password') }}
                </flux:link>
            @endif
        </div>

        <flux:checkbox name="remember" :label="__('auth.remember')" :checked="old('remember')" />

        <x-cta-button type="submit" variant="yellow" block data-test="login-button">
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
