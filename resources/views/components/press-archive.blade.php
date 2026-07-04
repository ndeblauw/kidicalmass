@props(['articlesByYear'])

{{-- Year-grouped press archive (PressArticle, admin-maintained), the left
     column of the Pers page. Distilled rows: the title is the link, with a
     muted 'outlet · short date' meta line under it (the year heading already
     carries the year) and the optional PDF link on that same line. Year
     labels are <h3>: they group the archive under the page's 'In de pers'
     h2, they don't head a section themselves. Sized in press-archive.css. --}}
<div class="press-archive">
    @foreach ($articlesByYear as $year => $articles)
        <h3 class="press-archive__year">{{ $year }}</h3>
        <ul class="press-archive__list" role="list">
            @foreach ($articles as $article)
                <li class="press-archive__item">
                    @if ($article->url)
                        <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer" class="press-archive__title">{{ $article->title }}</a>
                    @else
                        <span class="press-archive__title">{{ $article->title }}</span>
                    @endif
                    <span class="press-archive__meta">
                        <span class="press-archive__outlet">{{ $article->outlet }}</span>
                        <span aria-hidden="true">·</span>
                        <time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->isoFormat('D MMM') }}</time>
                        @if ($article->getFirstMedia('document'))
                            <span aria-hidden="true">·</span>
                            <a href="{{ $article->getFirstMediaUrl('document') }}" target="_blank" rel="noopener noreferrer" class="press-archive__document">{{ __('about.press_document_label') }}</a>
                        @endif
                    </span>
                </li>
            @endforeach
        </ul>
    @endforeach
</div>
