<div>
    <form wire:submit="updateProfileInformation" class="space-y-6">
        <flux:input wire:model="name" :label="__('Naam')" type="text" required autofocus autocomplete="name" />

        <div>
            <flux:input wire:model="email" :label="__('E-mail')" type="email" required autocomplete="email" />

            @if ($this->hasUnverifiedEmail)
                <div>
                    <flux:text class="mt-4">
                        {{ __('Je e-mailadres is niet geverifieerd.') }}

                        <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                            {{ __('Klik hier om de verificatie-e-mail opnieuw te versturen.') }}
                        </flux:link>
                    </flux:text>

                    @if (session('status') === 'verification-link-sent')
                        <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                            {{ __('Er is een nieuwe verificatielink naar je e-mailadres gestuurd.') }}
                        </flux:text>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit">{{ __('Opslaan') }}</flux:button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Opgeslagen.') }}
            </x-action-message>
        </div>
    </form>

    @if ($this->showDeleteUser)
        <livewire:settings.delete-user-form />
    @endif
</div>
