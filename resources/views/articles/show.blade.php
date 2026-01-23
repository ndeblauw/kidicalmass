<x-layouts::site title="{{ $article->title_nl }}">
    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('articles.index') }}" class="inline-flex items-center text-kidical-blue hover:text-kidical-orange transition-colors font-semibold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Articles
            </a>
        </div>

        <!-- Article Header -->
        <article class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-kidical-yellow to-kidical-orange p-8">
                <h1 class="text-4xl font-bold text-kidical-blue mb-4">{{ $article->title_nl }}</h1>
                <div class="flex items-center space-x-4 text-kidical-blue/80">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                        </svg>
                        <span class="font-semibold">{{ $article->author->name }}</span>
                    </div>
                    <span>•</span>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $article->created_at->format('F d, Y') }}</span>
                    </div>
                </div>
                @if($article->groups->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mt-4">
                        @foreach($article->groups as $group)
                            <a href="{{ route('groups.show', $group) }}" class="px-3 py-1 bg-white text-kidical-blue rounded-full text-sm font-semibold hover:bg-kidical-blue hover:text-white transition-colors">
                                {{ $group->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Article Content -->
            <div class="p-8">
                <div class="prose prose-lg max-w-none text-gray-800">
                    {!! nl2br(e($article->content_nl)) !!}
                </div>
            </div>
        </article>
    </div>
</x-layouts::site>
