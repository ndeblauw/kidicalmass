<div>
    <flux:card>
        <div class="flex items-center justify-between">
            <flux:heading size="lg">{{ __('Manage Group') }}</flux:heading>
            @if(!$showForm && $editingGroupId)
                <flux:button wire:click="edit" icon="pencil">
                    {{ __('Edit Group') }}
                </flux:button>
            @endif
        </div>

        @if($userGroups->count() > 1)
            <flux:separator class="my-4" />
            <flux:field>
                <flux:label>{{ __('Select Group') }}</flux:label>
                <flux:select wire:model.live="selectedGroupId">
                    @foreach($userGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>
        @endif

        @if($editingGroupId)
            @if($showForm)
                <flux:separator class="my-4" />
                <form wire:submit.prevent="save" class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Name') }}</flux:label>
                        <flux:input wire:model="name" placeholder="{{ __('Enter group name') }}" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Short Name') }}</flux:label>
                        <flux:input wire:model="shortname" placeholder="{{ __('Enter group short name') }}" />
                        <flux:error name="shortname" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('ZIP Code') }}</flux:label>
                        <flux:input wire:model="zip" placeholder="{{ __('Enter ZIP code') }}" />
                        <flux:error name="zip" />
                    </flux:field>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Started At') }}</flux:label>
                            <flux:input type="date" wire:model="started_at" />
                            <flux:error name="started_at" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Ended At') }}</flux:label>
                            <flux:input type="date" wire:model="ended_at" />
                            <flux:error name="ended_at" />
                        </flux:field>
                    </div>

                    <div class="flex gap-2">
                        <flux:button type="submit" variant="primary">
                            {{ __('Update Group') }}
                        </flux:button>
                        <flux:button wire:click="cancel" variant="ghost">
                            {{ __('Cancel') }}
                        </flux:button>
                    </div>
                </form>
            @else
                <flux:separator class="my-4" />
                <div class="space-y-4">
                    <div>
                        <flux:label>{{ __('Name') }}</flux:label>
                        <flux:text class="block mt-1">{{ $name }}</flux:text>
                    </div>

                    <div>
                        <flux:label>{{ __('Short Name') }}</flux:label>
                        <flux:text class="block mt-1">{{ $shortname }}</flux:text>
                    </div>

                    @if($zip)
                        <div>
                            <flux:label>{{ __('ZIP Code') }}</flux:label>
                            <flux:text class="block mt-1">{{ $zip }}</flux:text>
                        </div>
                    @endif

                    @if($started_at)
                        <div>
                            <flux:label>{{ __('Started At') }}</flux:label>
                            <flux:text class="block mt-1">{{ \Carbon\Carbon::parse($started_at)->format('M d, Y') }}</flux:text>
                        </div>
                    @endif

                    @if($ended_at)
                        <div>
                            <flux:label>{{ __('Ended At') }}</flux:label>
                            <flux:text class="block mt-1">{{ \Carbon\Carbon::parse($ended_at)->format('M d, Y') }}</flux:text>
                        </div>
                    @endif
                </div>
            @endif
        @endif
    </flux:card>

    @if($userGroups->count() == 0)
        <flux:card class="mt-6">
            <div class="text-center py-8">
                <flux:text>{{ __('You are not a member of any group yet. Contact an administrator to join a group.') }}</flux:text>
            </div>
        </flux:card>
    @endif
</div>
