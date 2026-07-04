@props(['article'])

@php
    $image = $article->getFirstMediaUrl('main', 'card');
    $date = $article->published_at ?? $article->created_at;
@endphp

{{-- PAT-17 · News preview / article card. The title's stretched link makes the
     whole card clickable; the group chips stay independently clickable on top
     of it (no nested anchors). --}}
<article
    {{ $attributes->merge(['class' => 'group relative h-full overflow-hidden rounded-tile border border-kidical-hairline bg-white shadow-float transition-[transform,box-shadow] duration-200 hover:-translate-y-1 hover:shadow-hover']) }}
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
        <h3 class="text-lg">
            <a href="{{ route('articles.show', $article) }}" class="link-plain text-kidical-blue transition-colors group-hover:text-kidical-orange after:absolute after:inset-0">{{ $article->title_nl }}</a>
        </h3>

        <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-semibold text-kidical-ink/50">
            @foreach ($article->groups as $group)
                <x-group-chip :group="$group" class="relative z-10" />
            @endforeach
            <time datetime="{{ $date->format('Y-m-d') }}">{{ $date->isoFormat('D MMM YYYY') }}</time>
        </p>
    </div>
</article>
