<x-layouts::site title="Kidical Mass - Belgium">
    <div class="space-y-12">
        <flux:heading level="1" class="text-center">Welcome to Kidical Mass Belgium</flux:heading>

        <flux:heading level="2">Latest Articles</flux:heading>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($latestArticles as $article)
                <flux:card>
                    <flux:heading level="3">{{ $article->title_nl }}</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                        By {{ $article->author->name }} • {{ $article->created_at->format('M d, Y') }}
                    </flux:text>
                    <flux:text>{{ Str::limit(strip_tags($article->content_nl), 150) }}</flux:text>
                    <div class="mt-4">
                        <flux:button href="{{ route('articles.show', $article) }}" variant="outline" size="sm">
                            Read More
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>

        <flux:heading level="2">Upcoming Activities</flux:heading>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($upcomingActivities as $activity)
                <flux:card>
                    <flux:heading level="3">{{ $activity->title_nl }}</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                        {{ $activity->begin_date->format('M d, Y \a\t H:i') }}
                    </flux:text>
                    <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                        📍 {{ $activity->location }}
                    </flux:text>
                    <flux:text>{{ Str::limit(strip_tags($activity->content_nl), 150) }}</flux:text>
                    <div class="mt-4">
                        <flux:button href="{{ route('activities.show', $activity) }}" variant="outline" size="sm">
                            View Activity
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>

        <flux:heading level="2">Groups</flux:heading>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($groups as $group)
                <flux:card>
                    <flux:heading level="3">{{ $group->name }}</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                        {{ $group->zip }} • {{ $group->articles_count }} articles • {{ $group->activities_count }} activities
                    </flux:text>
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
