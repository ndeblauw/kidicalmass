<x-layouts::site title="{{ $article->title_nl }}">
    <div class="max-w-4xl mx-auto space-y-8">
        <flux:heading level="1">{{ $article->title_nl }}</flux:heading>

        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
            <div class="flex items-center space-x-4">
                <flux:text class="text-gray-600 dark:text-gray-400">
                    By {{ $article->author->name }}
                </flux:text>
                <flux:text class="text-gray-600 dark:text-gray-400">
                    {{ $article->created_at->format('F d, Y') }}
                </flux:text>
            </div>
            @if($article->groups->isNotEmpty())
                <div class="flex flex-wrap gap-1">
                    @foreach($article->groups as $group)
                        <flux:badge>{{ $group->name }}</flux:badge>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="prose prose-lg dark:prose-invert max-w-none">
            <flux:text>{{ $article->content_nl }}</flux:text>
        </div>

        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <flux:button href="{{ route('articles.index') }}" variant="outline">
                ← Back to Articles
            </flux:button>
        </div>
    </div>
</x-layouts::site>
