<x-layouts::site title="{{ $activity->title_nl }}">
    <div class="max-w-4xl mx-auto space-y-8">
        <flux:heading level="1">{{ $activity->title_nl }}</flux:heading>
        
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div class="flex items-center space-x-2">
                    <flux:icon name="calendar" class="text-blue-600 dark:text-blue-400" />
                    <flux:text class="font-semibold">Date:</flux:text>
                    <flux:text>{{ $activity->begin_date->format('F d, Y \a\t H:i') }}</flux:text>
                </div>
                <div class="flex items-center space-x-2">
                    <flux:icon name="map-pin" class="text-blue-600 dark:text-blue-400" />
                    <flux:text class="font-semibold">Location:</flux:text>
                    <flux:text>{{ $activity->location }}</flux:text>
                </div>
                @if($activity->end_date && $activity->end_date->ne($activity->begin_date))
                    <div class="flex items-center space-x-2">
                        <flux:icon name="clock" class="text-blue-600 dark:text-blue-400" />
                        <flux:text class="font-semibold">End:</flux:text>
                        <flux:text>{{ $activity->end_date->format('F d, Y \a\t H:i') }}</flux:text>
                    </div>
                @endif
                <div class="flex items-center space-x-2">
                    <flux:icon name="user" class="text-blue-600 dark:text-blue-400" />
                    <flux:text class="font-semibold">Organizer:</flux:text>
                    <flux:text>{{ $activity->author->name }}</flux:text>
                </div>
            </div>
        </div>

        @if($activity->groups->isNotEmpty())
            <div class="flex flex-wrap gap-1">
                @foreach($activity->groups as $group)
                    <flux:badge>{{ $group->name }}</flux:badge>
                @endforeach
            </div>
        @endif

        <div class="prose prose-lg dark:prose-invert max-w-none">
            <flux:text>{{ $activity->content_nl }}</flux:text>
        </div>

        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <flux:button href="{{ route('activities.index') }}" variant="outline">
                ← Back to Activities
            </flux:button>
        </div>
    </div>
</x-layouts::site>