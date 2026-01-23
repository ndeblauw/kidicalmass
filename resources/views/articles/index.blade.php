<x-layouts::site title="Articles">
    <div class="space-y-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-kidical-blue mb-4">Articles</h1>
            <p class="text-lg text-gray-700">News, stories, and insights from Kidical Mass Belgium</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($articles as $article)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow border-t-4 border-kidical-yellow">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-kidical-blue mb-3 hover:text-kidical-orange transition-colors">
                            <a href="{{ route('articles.show', $article) }}">{{ $article->title_nl }}</a>
                        </h3>
                        <div class="flex items-center text-sm text-gray-600 mb-3">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                            </svg>
                            <span>{{ $article->author->name }}</span>
                            <span class="mx-2">•</span>
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ $article->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($article->groups->isNotEmpty())
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach($article->groups as $group)
                                    <span class="px-2 py-1 bg-kidical-light-yellow text-kidical-blue text-xs font-semibold rounded-full">
                                        {{ $group->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        <p class="text-gray-700 mb-4">{{ Str::limit(strip_tags($article->content_nl), 150) }}</p>
                        <a href="{{ route('articles.show', $article) }}" class="inline-block px-4 py-2 bg-kidical-green text-white rounded-lg hover:bg-kidical-orange transition-colors font-semibold text-sm">
                            Read More →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    </div>
</x-layouts::site>
