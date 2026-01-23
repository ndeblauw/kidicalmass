<x-layouts::site title="Groups">
    <div class="space-y-8">
        <flux:heading level="1">Groups</flux:heading>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($groups as $group)
                <flux:card>
                    <flux:heading level="3">{{ $group->name }}</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                        {{ $group->zip }}
                    </flux:text>
                    @if($group->parent)
                        <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                            Parent: {{ $group->parent->name }}
                        </flux:text>
                    @endif
                    <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                        {{ $group->articles_count }} articles • {{ $group->activities_count }} activities
                    </flux:text>
                    @if($group->children->isNotEmpty())
                        <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                            {{ $group->children->count() }} subgroups
                        </flux:text>
                    @endif
                    <div class="mt-4">
                        <flux:button href="{{ route('groups.show', $group) }}" variant="outline" size="sm">
                            View Group
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>
    </div>
</x-layouts::site>
