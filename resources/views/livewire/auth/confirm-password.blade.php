<x-layouts::auth :title="__('auth.confirm_title')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-5">
        @csrf

        <flux:input type="password" id="confirm-password" name="password" :label="__('auth.password_label')" viewable required autocomplete="current-password" aria-describedby="confirm-password-error" error:id="confirm-password-error" />

        <x-cta-button type="submit" variant="yellow" class="self-start" data-test="confirm-password-button">
            {{ __('auth.confirm_button') }}
        </x-cta-button>
    </form>
</x-layouts::auth>
