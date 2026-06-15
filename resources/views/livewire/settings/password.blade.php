<section class="w-full">
    <flux:heading class="sr-only">{{ __('Wachtwoordinstellingen') }}</flux:heading>

    <x-settings.layout :heading="__('Wachtwoord bijwerken')" :subheading="__('Zorg dat je account een lang, willekeurig wachtwoord gebruikt om veilig te blijven')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6">
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
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Opslaan') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('Opgeslagen.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
