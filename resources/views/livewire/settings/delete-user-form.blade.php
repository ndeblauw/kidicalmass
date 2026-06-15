<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <flux:heading>{{ __('Account verwijderen') }}</flux:heading>
        <flux:subheading>{{ __('Verwijder je account en al zijn gegevens') }}</flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
            {{ __('Account verwijderen') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form method="POST" wire:submit="deleteUser" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Weet je zeker dat je je account wilt verwijderen?') }}</flux:heading>

                <flux:subheading>
                    {{ __('Zodra je account is verwijderd, worden al je gegevens permanent verwijderd. Voer je wachtwoord in om te bevestigen dat je je account permanent wilt verwijderen.') }}
                </flux:subheading>
            </div>

            <flux:input wire:model="password" :label="__('Wachtwoord')" type="password" />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Annuleren') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit">{{ __('Account verwijderen') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
