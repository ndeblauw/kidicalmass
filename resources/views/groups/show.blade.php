<x-layouts::site title="{{ $group->name }}">
    <div class="max-w-6xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('groups.index') }}" class="inline-flex items-center text-kidical-blue hover:text-kidical-orange transition-colors font-semibold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Groups
            </a>
        </div>

        <!-- Group Header -->
        <div class="bg-gradient-to-r from-kidical-blue to-kidical-green text-white rounded-xl shadow-lg p-8 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-4">{{ $group->name }}</h1>
                    <div class="flex items-center space-x-3 text-white/90">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold text-lg">{{ $group->zip }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2 text-center">
                        <div class="text-2xl font-bold">{{ $group->articles_count }}</div>
                        <div class="text-sm opacity-90">Articles</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2 text-center">
                        <div class="text-2xl font-bold">{{ $group->activities_count }}</div>
                        <div class="text-sm opacity-90">Activities</div>
                    </div>
                </div>
            </div>
            @if($group->parent)
                <div class="mt-4 pt-4 border-t border-white/30">
                    <span class="text-white/80">Part of:</span>
                    <a href="{{ route('groups.show', $group->parent) }}" class="ml-2 px-3 py-1 bg-white text-kidical-blue rounded-full text-sm font-semibold hover:bg-kidical-yellow transition-colors">
                        {{ $group->parent->name }}
                    </a>
                </div>
            @endif
        </div>

        <div class="space-y-12">
            <!-- Subgroups -->
            @if($group->children->isNotEmpty())
                <section>
                    <h2 class="text-3xl font-bold text-kidical-blue mb-6">Subgroups</h2>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($group->children as $child)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow border-l-4 border-kidical-blue">
                                <div class="p-5">
                                    <h3 class="text-lg font-bold text-kidical-blue mb-2">{{ $child->name }}</h3>
                                    <p class="text-gray-600 mb-3">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $child->zip }}
                                    </p>
                                    <a href="{{ route('groups.show', $child) }}" class="inline-block px-4 py-2 bg-kidical-green text-white rounded-lg hover:bg-kidical-orange transition-colors font-semibold text-sm">
                                        View Subgroup →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Articles from Parent Groups -->
            @if($inheritedArticles->isNotEmpty())
                <section class="bg-gray-50 -mx-4 px-4 py-8 md:-mx-0 md:px-8 md:rounded-xl">
                    <h2 class="text-2xl font-bold text-kidical-blue mb-6">Articles from Parent Groups</h2>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($inheritedArticles as $article)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-l-2 border-gray-300">
                                <div class="p-5">
                                    <h3 class="text-lg font-bold text-kidical-blue mb-2 hover:text-kidical-orange transition-colors">
                                        <a href="{{ route('articles.show', $article) }}">{{ $article->title_nl }}</a>
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-3">
                                        By {{ $article->author->name }} • {{ $article->created_at->format('M d, Y') }}
                                    </p>
                                    <p class="text-gray-700 text-sm mb-3">{{ Str::limit(strip_tags($article->content_nl), 100) }}</p>
                                    <a href="{{ route('articles.show', $article) }}" class="text-kidical-green hover:text-kidical-orange transition-colors font-semibold text-sm">
                                        Read More →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Group Articles -->
            @if($articles->isNotEmpty())
                <section>
                    <h2 class="text-3xl font-bold text-kidical-blue mb-6">Articles</h2>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($articles as $article)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow border-t-4 border-kidical-yellow">
                                <div class="p-5">
                                    <h3 class="text-lg font-bold text-kidical-blue mb-2 hover:text-kidical-orange transition-colors">
                                        <a href="{{ route('articles.show', $article) }}">{{ $article->title_nl }}</a>
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-3">
                                        By {{ $article->author->name }} • {{ $article->created_at->format('M d, Y') }}
                                    </p>
                                    <p class="text-gray-700 text-sm mb-3">{{ Str::limit(strip_tags($article->content_nl), 100) }}</p>
                                    <a href="{{ route('articles.show', $article) }}" class="inline-block px-4 py-2 bg-kidical-green text-white rounded-lg hover:bg-kidical-orange transition-colors font-semibold text-sm">
                                        Read More →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Activities from Parent Groups -->
            @if($inheritedActivities->isNotEmpty())
                <section class="bg-gray-50 -mx-4 px-4 py-8 md:-mx-0 md:px-8 md:rounded-xl">
                    <h2 class="text-2xl font-bold text-kidical-blue mb-6">Activities from Parent Groups</h2>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($inheritedActivities as $activity)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-l-2 border-gray-300">
                                <div class="p-5">
                                    <h3 class="text-lg font-bold text-kidical-blue mb-2 hover:text-kidical-orange transition-colors">
                                        <a href="{{ route('activities.show', $activity) }}">{{ $activity->title_nl }}</a>
                                    </h3>
                                    <p class="text-sm text-gray-700 mb-2">
                                        <svg class="w-4 h-4 inline-block mr-1 text-kidical-green" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $activity->begin_date->format('M d, Y \a\t H:i') }}
                                    </p>
                                    <p class="text-sm text-gray-700 mb-3">
                                        <svg class="w-4 h-4 inline-block mr-1 text-kidical-green" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $activity->location }}
                                    </p>
                                    <p class="text-gray-700 text-sm mb-3">{{ Str::limit(strip_tags($activity->content_nl), 100) }}</p>
                                    <a href="{{ route('activities.show', $activity) }}" class="text-kidical-green hover:text-kidical-orange transition-colors font-semibold text-sm">
                                        View Activity →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Group Activities -->
            @if($activities->isNotEmpty())
                <section>
                    <h2 class="text-3xl font-bold text-kidical-blue mb-6">Activities</h2>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($activities as $activity)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow border-l-4 border-kidical-green">
                                <div class="p-5">
                                    <h3 class="text-lg font-bold text-kidical-blue mb-2 hover:text-kidical-orange transition-colors">
                                        <a href="{{ route('activities.show', $activity) }}">{{ $activity->title_nl }}</a>
                                    </h3>
                                    <p class="text-sm text-gray-700 mb-2">
                                        <svg class="w-4 h-4 inline-block mr-1 text-kidical-green" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $activity->begin_date->format('M d, Y \a\t H:i') }}
                                    </p>
                                    <p class="text-sm text-gray-700 mb-3">
                                        <svg class="w-4 h-4 inline-block mr-1 text-kidical-green" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $activity->location }}
                                    </p>
                                    <p class="text-gray-700 text-sm mb-3">{{ Str::limit(strip_tags($activity->content_nl), 100) }}</p>
                                    <a href="{{ route('activities.show', $activity) }}" class="inline-block px-4 py-2 bg-kidical-orange text-white rounded-lg hover:bg-kidical-blue transition-colors font-semibold text-sm">
                                        View Activity →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-layouts::site>
