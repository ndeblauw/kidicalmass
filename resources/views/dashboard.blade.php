<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        @php
            $user = auth()->user()->loadMissing('groups');
            $userGroups = $user->groups->sortBy('name');
        @endphp

        <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
        <flux:subheading>{{ __('Manage your groups, articles, and activities') }}</flux:subheading>

        <flux:card>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <flux:heading size="md">{{ __('Your Groups') }}</flux:heading>
                    <flux:text class="text-sm text-gray-600">
                        {{ trans_choice('You belong to :count group|You belong to :count groups', $userGroups->count(), ['count' => $userGroups->count()]) }}
                    </flux:text>
                </div>

                <flux:button href="{{ route('groups.index') }}" variant="ghost" size="sm">
                    {{ __('Browse all groups') }}
                </flux:button>
            </div>

            <flux:separator class="my-4" />

            @if($userGroups->isNotEmpty())
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($userGroups as $group)
                        <flux:card class="h-full">
                            <div class="flex items-start justify-between gap-3">
                                <div class="space-y-1">
                                    <flux:text class="font-semibold leading-tight">{{ $group->name }}</flux:text>
                                    <flux:text class="text-sm text-gray-600">{{ $group->shortname }}</flux:text>
                                </div>

                                <flux:button href="{{ route('groups.show', $group) }}" size="sm" variant="ghost">
                                    {{ __('Open') }}
                                </flux:button>
                            </div>

                            @if($group->zip)
                                <flux:text class="mt-3 text-sm text-gray-700">{{ __('ZIP: :zip', ['zip' => $group->zip]) }}</flux:text>
                            @endif

                            @if($group->started_at || $group->ended_at)
                                <div class="mt-2 text-sm text-gray-700">
                                    @if($group->started_at)
                                        <span>{{ __('Started:') }} {{ $group->started_at->toFormattedDateString() }}</span>
                                    @endif

                                    @if($group->ended_at)
                                        <span class="ml-2">{{ __('Ended:') }} {{ $group->ended_at->toFormattedDateString() }}</span>
                                    @endif
                                </div>
                            @endif
                        </flux:card>
                    @endforeach
                </div>
            @else
                <div class="py-4 text-center">
                    <flux:text>{{ __('You are not a member of any group yet. Contact an administrator to join a group.') }}</flux:text>
                </div>
            @endif
        </flux:card>

        @if($userGroups->count() > 0)
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
                    <livewire:dashboard.manage-group />
                </flux:tab.panel>
            </flux:tabs>
        @endif
    </div>
</x-layouts::app>
