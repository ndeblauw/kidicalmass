<div>
    <flux:card>
        <div class="flex items-center justify-between">
            <flux:heading size="lg">{{ __('Manage Activities') }}</flux:heading>
            @if(!$showForm)
                <flux:button wire:click="create" icon="plus">
                    {{ __('New Activity') }}
                </flux:button>
            @endif
        </div>

        @if($showForm)
            <flux:separator class="my-4" />
            <form wire:submit.prevent="save" class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Title') }}</flux:label>
                    <flux:input wire:model="title" placeholder="{{ __('Enter activity title') }}" />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Description') }}</flux:label>
                    <flux:textarea wire:model="description" rows="6" placeholder="{{ __('Enter activity description') }}" />
                    <flux:error name="description" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Location') }}</flux:label>
                    <flux:input wire:model="location" placeholder="{{ __('Enter activity location') }}" />
                    <flux:error name="location" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Begin Date') }}</flux:label>
                        <flux:input type="datetime-local" wire:model="begin_date" />
                        <flux:error name="begin_date" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('End Date') }}</flux:label>
                        <flux:input type="datetime-local" wire:model="end_date" />
                        <flux:error name="end_date" />
                    </flux:field>
                </div>

                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary">
                        {{ $editingActivityId ? __('Update Activity') : __('Create Activity') }}
                    </flux:button>
                    <flux:button wire:click="cancel" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                </div>
            </form>
        @endif
    </flux:card>

    @if($activities->count() > 0)
        <div class="mt-6 space-y-4">
            @foreach($activities as $activity)
                <flux:card>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <flux:heading size="md">{{ $activity->title }}</flux:heading>
                            <flux:subheading class="mt-1">
                                {{ __('By') }} {{ $activity->author->name }} 
                                &bull; {{ $activity->created_at->diffForHumans() }}
                            </flux:subheading>
                            <flux:text class="mt-2">{{ Str::limit($activity->description, 200) }}</flux:text>
                            @if($activity->location)
                                <div class="mt-2 flex items-center gap-1 text-sm text-zinc-500">
                                    <flux:icon.map-pin variant="mini" />
                                    {{ $activity->location }}
                                </div>
                            @endif
                            <div class="mt-2 flex items-center gap-1 text-sm text-zinc-500">
                                <flux:icon.calendar variant="mini" />
                                {{ $activity->begin_date->format('M d, Y H:i') }}
                                @if($activity->end_date)
                                    - {{ $activity->end_date->format('M d, Y H:i') }}
                                @endif
                            </div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($activity->groups as $group)
                                    <flux:badge>{{ $group->name }}</flux:badge>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <flux:button wire:click="edit({{ $activity->id }})" size="sm" icon="pencil" variant="ghost">
                            </flux:button>
                            <flux:button 
                                wire:click="delete({{ $activity->id }})" 
                                wire:confirm="{{ __('Are you sure you want to delete this activity?') }}"
                                size="sm" 
                                icon="trash" 
                                variant="danger"
                            >
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @else
        @if(!$showForm)
            <flux:card class="mt-6">
                <div class="text-center py-8">
                    <flux:text>{{ __('No activities found. Create your first activity!') }}</flux:text>
                </div>
            </flux:card>
        @endif
    @endif
</div>
