@props(['article'])

@php
    $media = $article->getFirstMedia('main');
    $date = $article->published_at ?? $article->created_at;
@endphp

{{-- News feature — the newest article as a wide horizontal card: image left
     (~55%), text right; stacks image-above-text on mobile. Quiet tier like
     x-article-card: white, upright, hairline + float shadow, same hover lift.
     The title's stretched link makes the whole card clickable; group chips
     stay independently clickable (no nested anchors). The data-article-feature
     seam is the stable hook ArticlePublishingTest asserts (never the utilities). --}}
<article
    data-article-feature
    {{ $attributes->merge(['class' => 'group relative overflow-hidden rounded-card border border-kidical-hairline bg-white shadow-float transition-[transform,box-shadow] duration-200 hover:-translate-y-1 hover:shadow-hover'.($media ? ' md:grid md:grid-cols-[55fr_45fr]' : '')]) }}
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
        <h2>
            <a href="{{ route('articles.show', $article) }}" class="link-plain text-kidical-ink transition-colors group-hover:text-kidical-blue after:absolute after:inset-0">{{ $article->title_nl }}</a>
        </h2>

        <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm font-semibold text-kidical-ink/50">
            @foreach ($article->groups as $group)
                <x-group-chip :group="$group" class="relative z-10" />
            @endforeach
            <time datetime="{{ $date->format('Y-m-d') }}">{{ $date->isoFormat('D MMMM YYYY') }}</time>
        </p>
    </div>
</article>
