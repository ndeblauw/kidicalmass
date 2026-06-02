<x-layouts::site title="{{ $article->title_nl }}">
    <article class="mx-auto max-w-3xl space-y-8">
        <a href="{{ route('articles.index') }}" class="inline-block font-semibold text-kidical-blue hover:underline">← Back to articles</a>

        <header class="space-y-4">
            <h1>{{ $article->title_nl }}</h1>
            <p class="font-semibold text-kidical-ink/60">
                @if ($article->author)
                    {{ $article->author->name }} ·
                @endif
                <time datetime="{{ $article->created_at->format('Y-m-d') }}">{{ $article->created_at->format('j F Y') }}</time>
            </p>
            @if ($article->groups->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach ($article->groups as $group)
                        <a href="{{ route('groups.show', $group) }}" class="link-plain inline-block rounded-full border border-kidical-ink/15 bg-white px-3 py-1 text-sm font-semibold text-kidical-blue transition-colors hover:border-kidical-blue">
                            {{ $group->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </header>

        @if ($article->getFirstMedia('main'))
            <div class="aspect-[16/9] overflow-hidden rounded-xl">
                <img src="{{ $article->getFirstMediaUrl('main') }}" alt="{{ $article->title_nl }}" class="h-full w-full object-cover">
            </div>
        @endif

        <div class="text-lg leading-relaxed text-kidical-ink">
            {!! nl2br(e($article->content_nl)) !!}
        </div>

        @if ($article->getMedia('gallery')->count() > 0)
            <section class="space-y-4">
                <h2 class="text-2xl text-kidical-ink">Gallery</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($article->getMedia('gallery') as $media)
                        <div class="aspect-[4/3] overflow-hidden rounded-xl">
                            <img src="{{ $media->getUrl() }}" alt="{{ $article->title_nl }}" class="h-full w-full object-cover transition-transform duration-300 hover:scale-105">
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </article>
</x-layouts::site>
