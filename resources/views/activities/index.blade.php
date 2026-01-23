<x-layouts::site title="Activities">
    <div class="space-y-8">
        <flux:heading level="1">Activities</flux:heading>
        
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($activities as $activity)
                <flux:card>
                    <flux:heading level="3">{{ $activity->title_nl }}</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                        {{ $activity->begin_date->format('M d, Y \a\t H:i') }}
                    </flux:text>
                    <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                        📍 {{ $activity->location }}
                    </flux:text>
                    @if($activity->groups->isNotEmpty())
                        <div class="flex flex-wrap gap-1 mb-2">
                            @foreach($activity->groups as $group)
                                <flux:badge size="sm">{{ $group->name }}</flux:badge>
                            @endforeach
                        </div>
                    @endif
                    <flux:text>{{ Str::limit(strip_tags($activity->content_nl), 150) }}</flux:text>
                    <div class="mt-4">
                        <flux:button href="{{ route('activities.show', $activity) }}" variant="outline" size="sm">
                            View Activity
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>

        {{ $activities->links() }}
    </div>
</x-layouts::site>