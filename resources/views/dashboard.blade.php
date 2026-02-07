<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
        <flux:subheading>{{ __('Manage your groups, articles, and activities') }}</flux:subheading>

        @if(auth()->user()->groups->count() > 0)
            <flux:tabs wire:model="activeTab">
                <flux:tab name="articles">{{ __('Articles') }}</flux:tab>
                <flux:tab name="activities">{{ __('Activities') }}</flux:tab>
                <flux:tab name="groups">{{ __('Groups') }}</flux:tab>

                <flux:tab.panel name="articles">
                    <livewire:dashboard.manage-articles />
                </flux:tab.panel>

                <flux:tab.panel name="activities">
                    <livewire:dashboard.manage-activities />
                </flux:tab.panel>

                <flux:tab.panel name="groups">
                    <div class="py-4">{{ __('Group management coming soon...') }}</div>
                </flux:tab.panel>
            </flux:tabs>
        @else
            <flux:card>
                <div class="text-center py-8">
                    <flux:text>{{ __('You are not a member of any group yet. Contact an administrator to join a group.') }}</flux:text>
                </div>
            </flux:card>
        @endif
    </div>
</x-layouts::app>
