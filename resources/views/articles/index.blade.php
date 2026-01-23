<x-layouts::site title="Articles">
    <div class="space-y-8">
        <flux:heading level="1">Articles</flux:heading>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($articles as $article)
                <flux:card>
                    <flux:heading level="3">{{ $article->title_nl }}</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400 mb-2">
                        By {{ $article->author->name }} • {{ $article->created_at->format('M d, Y') }}
                    </flux:text>
                    @if($article->groups->isNotEmpty())
                        <div class="flex flex-wrap gap-1 mb-2">
                            @foreach($article->groups as $group)
                                <flux:badge size="sm">{{ $group->name }}</flux:badge>
                            @endforeach
                        </div>
                    @endif
                    <flux:text>{{ Str::limit(strip_tags($article->content_nl), 150) }}</flux:text>
                    <div class="mt-4">
                        <flux:button href="{{ route('articles.show', $article) }}" variant="outline" size="sm">
                            Read More
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>

        {{ $articles->links() }}
    </div>
</x-layouts::site>
