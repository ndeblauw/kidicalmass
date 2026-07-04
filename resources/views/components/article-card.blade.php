@props(['article'])

@php
    $image = $article->getFirstMediaUrl('main', 'card');
@endphp

{{-- PAT-17 · News preview / article card. Whole card links to the article. --}}
<a
    href="{{ route('articles.show', $article) }}"
    {{ $attributes->merge(['class' => 'link-plain group block h-full overflow-hidden rounded-tile border border-kidical-hairline bg-white shadow-float transition-[transform,box-shadow] duration-200 hover:-translate-y-1 hover:shadow-hover']) }}
>
    @if ($image)
        <div class="aspect-[16/9] overflow-hidden">
            <img
                src="{{ $image }}"
                alt="{{ $article->title_nl }}"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy"
                decoding="async"
            >
        </div>
    @endif

    <div class="p-5">
        <h3 class="text-lg text-kidical-blue group-hover:text-kidical-orange transition-colors">{{ $article->title_nl }}</h3>

        <p class="mt-1 text-xs font-semibold text-kidical-ink/50">
            @if ($article->author)
                {{ $article->author->name }} ·
            @endif
            <time datetime="{{ ($article->published_at ?? $article->created_at)->format('Y-m-d') }}">{{ ($article->published_at ?? $article->created_at)->isoFormat('D MMM YYYY') }}</time>
        </p>

        <p class="mt-2 text-sm">{{ Str::limit(strip_tags($article->content_nl), 140, preserveWords: true) }}</p>
    </div>
</a>
