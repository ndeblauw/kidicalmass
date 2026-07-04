@props(['article'])

@php
    $media = $article->getFirstMedia('main');
@endphp

{{-- News feature — the newest article as a wide horizontal card: image left
     (~55%), text right; stacks image-above-text on mobile. Quiet tier like
     x-article-card: white, upright, hairline + float shadow, same hover lift.
     Whole card links to the article. The data-article-feature seam is the
     stable hook ArticlePublishingTest asserts (never the utilities). --}}
<a
    href="{{ route('articles.show', $article) }}"
    data-article-feature
    {{ $attributes->merge(['class' => 'link-plain group block overflow-hidden rounded-card border border-kidical-hairline bg-white shadow-float transition-[transform,box-shadow] duration-200 hover:-translate-y-1 hover:shadow-hover'.($media ? ' md:grid md:grid-cols-[55fr_45fr]' : '')]) }}
>
    @if ($media)
        <div class="aspect-[16/9] overflow-hidden md:aspect-auto md:h-full">
            <img
                src="{{ $media->getUrl() }}"
                @if ($media->getSrcset()) srcset="{{ $media->getSrcset() }}" sizes="(min-width: 768px) 560px, 100vw" @endif
                alt=""
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                fetchpriority="high"
            >
        </div>
    @endif

    <div class="flex h-full flex-col justify-center gap-3 p-6 md:p-10">
        <h2 class="transition-colors group-hover:text-kidical-blue">{{ $article->title_nl }}</h2>

        <p class="text-sm font-semibold text-kidical-ink/50">
            @if ($article->author)
                {{ $article->author->name }} ·
            @endif
            <time datetime="{{ ($article->published_at ?? $article->created_at)->format('Y-m-d') }}">{{ ($article->published_at ?? $article->created_at)->isoFormat('D MMMM YYYY') }}</time>
        </p>

        <p class="text-base">{{ Str::limit(strip_tags($article->content_nl), 240, preserveWords: true) }}</p>
    </div>
</a>
