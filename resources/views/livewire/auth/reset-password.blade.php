<x-layouts::auth :title="__('auth.reset_title')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5">
        @csrf
        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <flux:input type="email" id="reset-email" name="email" :label="__('auth.email')" value="{{ request('email') }}" required autocomplete="email" aria-describedby="reset-email-error" error:id="reset-email-error" />

        <flux:input type="password" id="reset-password" name="password" :label="__('auth.password_label')" viewable required autocomplete="new-password" aria-describedby="reset-password-error" error:id="reset-password-error" />

        <flux:input type="password" id="reset-password-confirmation" name="password_confirmation" :label="__('auth.password_confirm_label')" viewable required autocomplete="new-password" />

        <x-cta-button type="submit" variant="yellow" class="self-start" data-test="reset-password-button">
            {{ __('auth.reset_button') }}
        </x-cta-button>
    </form>
</x-layouts::auth>
