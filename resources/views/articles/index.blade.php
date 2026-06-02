<x-layouts::site title="Articles">
    <div class="space-y-8">
        <div class="text-center mb-12">
            <h1>Articles</h1>
            <flux:text>News, stories, and insights from Kidical Mass Belgium</flux:text>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($articles as $article)
                <article class="overflow-hidden">
                    @if($article->getFirstMedia('main'))
                        <div class="aspect-[4/3] overflow-hidden">
                            <img src="{{ $article->getFirstMediaUrl('main', 'card') }}"
                                 alt="{{ $article->title_nl }}"
                                 class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="p-6">
                        <h3 class="mb-3">
                            <a href="{{ route('articles.show', $article) }}">{{ $article->title_nl }}</a>
                        </h3>
                        <dl class="flex items-center gap-3 mb-3">
                            <div class="flex items-center gap-1">
                                <flux:icon.user aria-hidden="true" />
                                <dt class="sr-only">Author</dt>
                                <dd>{{ $article->author->name }}</dd>
                            </div>
                            <span aria-hidden="true">•</span>
                            <div class="flex items-center gap-1">
                                <flux:icon.calendar-days aria-hidden="true" />
                                <dt class="sr-only">Date</dt>
                                <dd><time datetime="{{ $article->created_at->format('Y-m-d') }}">{{ $article->created_at->format('M d, Y') }}</time></dd>
                            </div>
                        </dl>
                        @if($article->groups->isNotEmpty())
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach($article->groups as $group)
                                    <flux:badge href="{{ route('groups.show', $group) }}">{{ $group->name }}</flux:badge>
                                @endforeach
                            </div>
                        @endif
                        <flux:text class="mb-4">{{ Str::limit(strip_tags($article->content_nl), 150) }}</flux:text>
                        <flux:button href="{{ route('articles.show', $article) }}" variant="primary">Read More</flux:button>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    </div>
</x-layouts::site>
