<x-layouts::auth :title="__('auth.reset_title')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5">
        @csrf
        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <flux:input
            name="email"
            value="{{ request('email') }}"
            :label="__('auth.email')"
            type="email"
            required
            autocomplete="email"
        />

        <flux:input
            name="password"
            :label="__('auth.password_label')"
            type="password"
            required
            autocomplete="new-password"
            viewable
        />

        <flux:input
            name="password_confirmation"
            :label="__('auth.password_confirm_label')"
            type="password"
            required
            autocomplete="new-password"
            viewable
        />

        <x-cta-button type="submit" variant="yellow" class="self-start" data-test="reset-password-button">
            {{ __('auth.reset_button') }}
        </x-cta-button>
    </form>
</x-layouts::auth>
