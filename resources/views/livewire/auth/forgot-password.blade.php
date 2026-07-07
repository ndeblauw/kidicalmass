<x-layouts::auth :title="__('auth.forgot_title')" :intro="__('auth.forgot_intro')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
        @csrf

        <flux:input
            name="email"
            :label="__('auth.email')"
            type="email"
            required
            autofocus
            placeholder="naam@voorbeeld.be"
        />

        <x-cta-button type="submit" variant="yellow" class="self-start" data-test="email-password-reset-link-button">
            {{ __('auth.forgot_button') }}
        </x-cta-button>
    </form>

    <p class="mt-6 text-center text-sm">
        <flux:link :href="route('login')" wire:navigate>{{ __('auth.back_to_login') }}</flux:link>
    </p>
</x-layouts::auth>
