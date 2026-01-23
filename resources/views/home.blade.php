<x-layouts::site title="Kidical Mass - Belgium">
    <!-- Hero Section -->
    <div class="hero-section -mx-4 -mt-8 mb-12 px-4 py-16 md:py-24 text-center">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-bold text-kidical-blue mb-6">
                <x-bike-icon class="w-12 h-12 md:w-16 md:h-16 inline-block mr-2" />
                Kidical Mass Belgium
            </h1>
            <p class="text-xl md:text-2xl text-kidical-blue/90 mb-8">
                Safe, fun, and accessible cycling for families and children
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('activities.index') }}" class="px-8 py-3 bg-kidical-green text-white rounded-lg hover:bg-kidical-blue transition-colors font-bold text-lg shadow-lg">
                    View Activities
                </a>
                <a href="{{ route('groups.index') }}" class="px-8 py-3 bg-white text-kidical-blue rounded-lg hover:bg-kidical-light-yellow transition-colors font-bold text-lg shadow-lg">
                    Find Your Group
                </a>
            </div>
        </div>
    </div>

    <div class="space-y-16">
        <!-- Latest Articles -->
        <section>
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-kidical-blue">Latest Articles</h2>
                <a href="{{ route('articles.index') }}" class="text-kidical-green hover:text-kidical-orange transition-colors font-semibold">
                    View All →
                </a>
            </div>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($latestArticles as $article)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow border-t-4 border-kidical-yellow">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-kidical-blue mb-3">{{ $article->title_nl }}</h3>
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
                            <p class="text-gray-700 mb-4">{{ Str::limit(strip_tags($article->content_nl), 150) }}</p>
                            <a href="{{ route('articles.show', $article) }}" class="inline-block px-4 py-2 bg-kidical-green text-white rounded-lg hover:bg-kidical-orange transition-colors font-semibold text-sm">
                                Read More
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Upcoming Activities -->
        <section class="bg-kidical-light-yellow -mx-4 px-4 py-12 md:-mx-0 md:px-8 md:rounded-2xl">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-kidical-blue">Upcoming Activities</h2>
                <a href="{{ route('activities.index') }}" class="text-kidical-green hover:text-kidical-orange transition-colors font-semibold">
                    View All →
                </a>
            </div>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($upcomingActivities as $activity)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow border-l-4 border-kidical-green">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-kidical-blue mb-3">{{ $activity->title_nl }}</h3>
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-700">
                                    <svg class="w-4 h-4 mr-2 text-kidical-green" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-semibold">{{ $activity->begin_date->format('M d, Y \a\t H:i') }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-700">
                                    <svg class="w-4 h-4 mr-2 text-kidical-green" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $activity->location }}</span>
                                </div>
                            </div>
                            <p class="text-gray-700 mb-4">{{ Str::limit(strip_tags($activity->content_nl), 150) }}</p>
                            <a href="{{ route('activities.show', $activity) }}" class="inline-block px-4 py-2 bg-kidical-orange text-white rounded-lg hover:bg-kidical-blue transition-colors font-semibold text-sm">
                                View Activity
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Groups -->
        <section>
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-kidical-blue">Local Groups</h2>
                <a href="{{ route('groups.index') }}" class="text-kidical-green hover:text-kidical-orange transition-colors font-semibold">
                    View All →
                </a>
            </div>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($groups as $group)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow border-t-4 border-kidical-blue">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-kidical-blue mb-3">{{ $group->name }}</h3>
                            <div class="flex items-center text-sm text-gray-600 mb-3">
                                <svg class="w-4 h-4 mr-1 text-kidical-blue" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-semibold">{{ $group->zip }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 mb-4">
                                <span class="px-3 py-1 bg-kidical-light-yellow rounded-full text-kidical-blue font-semibold">
                                    {{ $group->articles_count }} articles
                                </span>
                                <span class="mx-2">•</span>
                                <span class="px-3 py-1 bg-kidical-light-yellow rounded-full text-kidical-blue font-semibold">
                                    {{ $group->activities_count }} activities
                                </span>
                            </div>
                            <a href="{{ route('groups.show', $group) }}" class="inline-block px-4 py-2 bg-kidical-blue text-white rounded-lg hover:bg-kidical-green transition-colors font-semibold text-sm">
                                View Group
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts::site>
