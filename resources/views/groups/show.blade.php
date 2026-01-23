<x-layouts::site title="{{ $group->name }}">
    <div class="max-w-6xl mx-auto space-y-8">
        <div class="flex items-center justify-between">
            <flux:heading level="1">{{ $group->name }}</flux:heading>
            <flux:text class="text-gray-600 dark:text-gray-400">
                {{ $group->zip }}
            </flux:text>
        </div>

        @if($group->parent)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <flux:text class="text-blue-800 dark:text-blue-200">
                    Part of: <flux:button href="{{ route('groups.show', $group->parent) }}" variant="ghost" size="sm">{{ $group->parent->name }}</flux:button>
                </flux:text>
            </div>
        @endif

        @if($group->children->isNotEmpty())
            <div>
                <flux:heading level="2">Subgroups</flux:heading>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($group->children as $child)
                        <flux:card>
                            <flux:heading level="4">{{ $child->name }}</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                                {{ $child->zip }}
                            </flux:text>
                            <div class="mt-2">
                                <flux:button href="{{ route('groups.show', $child) }}" variant="outline" size="sm">
                                    View Subgroup
                                </flux:button>
                            </div>
                        </flux:card>
                    @endforeach
                </div>
            </div>
        @endif

        @if($inheritedArticles->isNotEmpty())
            <div>
                <flux:heading level="2">Articles from Parent Groups</flux:heading>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($inheritedArticles as $article)
                        <flux:card variant="outline">
                            <flux:heading level="4">{{ $article->title_nl }}</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                                By {{ $article->author->name }} • {{ $article->created_at->format('M d, Y') }}
                            </flux:text>
                            <flux:text>{{ Str::limit(strip_tags($article->content_nl), 100) }}</flux:text>
                            <div class="mt-2">
                                <flux:button href="{{ route('articles.show', $article) }}" variant="ghost" size="sm">
                                    Read More
                                </flux:button>
                            </div>
                        </flux:card>
                    @endforeach
                </div>
            </div>
        @endif

        @if($articles->isNotEmpty())
            <div>
                <flux:heading level="2">Articles</flux:heading>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($articles as $article)
                        <flux:card>
                            <flux:heading level="4">{{ $article->title_nl }}</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                                By {{ $article->author->name }} • {{ $article->created_at->format('M d, Y') }}
                            </flux:text>
                            <flux:text>{{ Str::limit(strip_tags($article->content_nl), 100) }}</flux:text>
                            <div class="mt-2">
                                <flux:button href="{{ route('articles.show', $article) }}" variant="outline" size="sm">
                                    Read More
                                </flux:button>
                            </div>
                        </flux:card>
                    @endforeach
                </div>
            </div>
        @endif

        @if($inheritedActivities->isNotEmpty())
            <div>
                <flux:heading level="2">Activities from Parent Groups</flux:heading>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($inheritedActivities as $activity)
                        <flux:card variant="outline">
                            <flux:heading level="4">{{ $activity->title_nl }}</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                                {{ $activity->begin_date->format('M d, Y \a\t H:i') }}
                            </flux:text>
                            <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                                📍 {{ $activity->location }}
                            </flux:text>
                            <flux:text>{{ Str::limit(strip_tags($activity->content_nl), 100) }}</flux:text>
                            <div class="mt-2">
                                <flux:button href="{{ route('activities.show', $activity) }}" variant="ghost" size="sm">
                                    View Activity
                                </flux:button>
                            </div>
                        </flux:card>
                    @endforeach
                </div>
            </div>
        @endif

        @if($activities->isNotEmpty())
            <div>
                <flux:heading level="2">Activities</flux:heading>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($activities as $activity)
                        <flux:card>
                            <flux:heading level="4">{{ $activity->title_nl }}</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                                {{ $activity->begin_date->format('M d, Y \a\t H:i') }}
                            </flux:text>
                            <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                                📍 {{ $activity->location }}
                            </flux:text>
                            <flux:text>{{ Str::limit(strip_tags($activity->content_nl), 100) }}</flux:text>
                            <div class="mt-2">
                                <flux:button href="{{ route('activities.show', $activity) }}" variant="outline" size="sm">
                                    View Activity
                                </flux:button>
                            </div>
                        </flux:card>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <flux:button href="{{ route('groups.index') }}" variant="outline">
                ← Back to Groups
            </flux:button>
        </div>
    </div>
</x-layouts::site>

