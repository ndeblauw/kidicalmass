<form method="POST" wire:submit="updatePassword" class="space-y-6">
    <flux:input
        wire:model="current_password"
        :label="__('Huidig wachtwoord')"
        type="password"
        required
        autocomplete="current-password"
    />
    <flux:input
        wire:model="password"
        :label="__('Nieuw wachtwoord')"
        type="password"
        required
        autocomplete="new-password"
    />
    <flux:input
        wire:model="password_confirmation"
        :label="__('Bevestig wachtwoord')"
        type="password"
        required
        autocomplete="new-password"
    />

    <div class="flex items-center gap-4">
        <flux:button variant="primary" type="submit">{{ __('Opslaan') }}</flux:button>

        <x-action-message class="me-3" on="password-updated">
            {{ __('Opgeslagen.') }}
        </x-action-message>
    </div>
</form>
