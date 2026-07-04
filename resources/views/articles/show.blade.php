<x-layouts::site title="{{ $article->title_nl }}" :description="$article->metaDescription()" :og-image="$article->ogImageUrl()" og-type="article">
    <article class="mx-auto max-w-3xl space-y-8">
        <a href="{{ route('articles.index') }}" class="inline-block font-semibold text-kidical-blue hover:underline">← {{ __('about.news_back') }}</a>

        <header class="space-y-4">
            <h1>{{ $article->title_nl }}</h1>
            <p class="font-semibold text-kidical-ink/60">
                @if ($article->author)
                    {{ $article->author->name }} ·
                @endif
                <time datetime="{{ ($article->published_at ?? $article->created_at)->format('Y-m-d') }}">{{ ($article->published_at ?? $article->created_at)->isoFormat('D MMMM YYYY') }}</time>
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

        @if ($mainMedia = $article->getFirstMedia('main'))
            <div class="aspect-[16/9] overflow-hidden rounded-xl">
                <img src="{{ $mainMedia->getUrl() }}" @if ($mainMedia->getSrcset()) srcset="{{ $mainMedia->getSrcset() }}" sizes="(min-width: 768px) 720px, 100vw" @endif alt="{{ $article->title_nl }}" class="h-full w-full object-cover" fetchpriority="high">
            </div>
        @endif

        <div class="article-body text-lg leading-relaxed text-kidical-ink">
            {!! $article->content_html !!}
        </div>

        @if ($article->getMedia('gallery')->count() > 0)
            <section class="space-y-4" aria-label="{{ __('about.news_gallery') }}">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($article->getMedia('gallery') as $media)
                        <div class="aspect-[4/3] overflow-hidden rounded-xl">
                            {{-- Alt: an admin-set "alt" custom property when present; never
                                 the article title repeated N times (a11y). --}}
                            <img src="{{ $media->getUrl() }}" @if ($media->getSrcset()) srcset="{{ $media->getSrcset() }}" sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw" @endif alt="{{ $media->getCustomProperty('alt', '') }}" class="h-full w-full object-cover transition-transform duration-300 hover:scale-105" loading="lazy" decoding="async">
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </article>

    {{-- MEER NIEUWS — neighbouring articles, so the read path has an exit
         besides the closing band the reader already saw on the feed. --}}
    @if ($newerArticle || $olderArticle)
        <nav class="mx-auto mt-16 max-w-3xl" aria-label="{{ __('about.news_more_title') }}" data-article-neighbours>
            <h2 class="mb-6">{{ __('about.news_more_title') }}</h2>
            <ul class="grid gap-6 sm:grid-cols-2" role="list">
                @if ($olderArticle)
                    <li>
                        <a href="{{ route('articles.show', $olderArticle) }}" class="link-plain group block">
                            <span class="block text-sm font-semibold text-kidical-ink/60">← {{ __('about.news_more_older') }}</span>
                            <span class="font-semibold text-kidical-blue group-hover:underline">{{ $olderArticle->title_nl }}</span>
                        </a>
                    </li>
                @endif
                @if ($newerArticle)
                    <li class="sm:col-start-2 sm:text-right">
                        <a href="{{ route('articles.show', $newerArticle) }}" class="link-plain group block">
                            <span class="block text-sm font-semibold text-kidical-ink/60">{{ __('about.news_more_newer') }} →</span>
                            <span class="font-semibold text-kidical-blue group-hover:underline">{{ $newerArticle->title_nl }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    @endif

    <x-slot:closing>
        <x-closing-cta heading="Zin gekregen om mee te rijden?"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>
</x-layouts::site>
