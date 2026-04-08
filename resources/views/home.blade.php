<x-layouts::site title="Kidical Mass - Belgium">
    <!-- Hero Section -->
    <div class="hero-section -mx-4 -mt-8 mb-12 px-4 py-16 md:py-24 text-center">
        <div class="max-w-4xl mx-auto">
            <h1 class="mb-6">
                <x-bike-icon class="w-12 h-12 md:w-16 md:h-16 inline-block mr-2" />
                Kidical Mass Belgium
            </h1>
            <flux:text class="mb-8">Safe, fun, and accessible cycling for families and children</flux:text>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <flux:button href="{{ route('activities.index') }}" variant="primary">View Activities</flux:button>
                <flux:button href="{{ route('groups.index') }}">Find Your Group</flux:button>
            </div>
        </div>
    </div>

    <div class="space-y-16">
        <!-- Latest Articles -->
        <section>
            <div class="flex items-center justify-between mb-8">
                <h2>Latest Articles</h2>
                <flux:button href="{{ route('articles.index') }}" variant="ghost">View All</flux:button>
            </div>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($latestArticles as $article)
                    <article class="overflow-hidden">
                        <div class="p-6">
                            <h3 class="mb-3">{{ $article->title_nl }}</h3>
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
                            <flux:text class="mb-4">{{ Str::limit(strip_tags($article->content_nl), 150) }}</flux:text>
                            <flux:button href="{{ route('articles.show', $article) }}" variant="primary">Read More</flux:button>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <!-- Upcoming Activities -->
        <section class="-mx-4 px-4 py-12 md:-mx-0 md:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2>Upcoming Activities</h2>
                <flux:button href="{{ route('activities.index') }}" variant="ghost">View All</flux:button>
            </div>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($upcomingActivities as $activity)
                    <article class="overflow-hidden">
                        <div class="p-6">
                            <h3 class="mb-3">{{ $activity->title_nl }}</h3>
                            <dl class="space-y-2 mb-4">
                                <div class="flex items-center gap-2">
                                    <flux:icon.calendar-days class="shrink-0" aria-hidden="true" />
                                    <div>
                                        <dt class="sr-only">Date</dt>
                                        <dd><time datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ $activity->begin_date->format('M d, Y \a\t H:i') }}</time></dd>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <flux:icon.map-pin class="shrink-0" aria-hidden="true" />
                                    <div>
                                        <dt class="sr-only">Location</dt>
                                        <dd>{{ $activity->location }}</dd>
                                    </div>
                                </div>
                            </dl>
                            <flux:text class="mb-4">{{ Str::limit(strip_tags($activity->content_nl), 150) }}</flux:text>
                            <flux:button href="{{ route('activities.show', $activity) }}" variant="primary">View Activity</flux:button>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <!-- Groups -->
        <section>
            <div class="flex items-center justify-between mb-8">
                <h2>Local Groups</h2>
                <flux:button href="{{ route('groups.index') }}" variant="ghost">View All</flux:button>
            </div>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($groups as $group)
                    <article class="overflow-hidden">
                        <div class="p-6">
                            <h3 class="mb-3">{{ $group->name }}</h3>
                            <dl class="mb-3">
                                <div class="flex items-center gap-1">
                                    <flux:icon.map-pin aria-hidden="true" />
                                    <dt class="sr-only">ZIP</dt>
                                    <dd>{{ $group->zip }}</dd>
                                </div>
                            </dl>
                            <div class="flex items-center gap-2 mb-4">
                                <flux:badge>{{ $group->articles_count }} articles</flux:badge>
                                <flux:badge>{{ $group->activities_count }} activities</flux:badge>
                            </div>
                            <flux:button href="{{ route('groups.show', $group) }}" variant="primary">View Group</flux:button>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts::site>
