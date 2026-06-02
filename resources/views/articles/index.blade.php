<x-layouts::site title="Articles">
    <div class="mx-auto max-w-5xl space-y-8">
        <header class="space-y-2">
            <h1>Articles</h1>
            <p class="text-xl text-kidical-ink/70">News and stories from Kidical Mass Belgium</p>
        </header>

        @if ($articles->isNotEmpty())
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <x-article-card :article="$article" />
                @endforeach
            </div>

            <div>{{ $articles->links() }}</div>
        @else
            <p class="text-kidical-ink/70">No articles yet.</p>
        @endif
    </div>
</x-layouts::site>
