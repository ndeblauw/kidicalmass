@props(['articlesByYear'])

{{-- Year-grouped press archive (PressArticle, admin-maintained), used on the
     Pers page's light-blue band under its "Persoverzicht" section heading.
     Year labels are <h3>: they group the archive under that h2, they don't
     head a section themselves. Sized in press-archive.css. --}}
<div class="press-archive">
    @foreach ($articlesByYear as $year => $articles)
        <h3 class="press-archive__year">{{ $year }}</h3>
        <ul class="press-archive__list" role="list">
            @foreach ($articles as $article)
                <li class="press-archive__item">
                    <span class="press-archive__meta">
                        <span class="press-archive__outlet">{{ $article->outlet }}</span>
                        <time datetime="{{ $article->published_at->toDateString() }}" class="press-archive__date">{{ $article->published_at->isoFormat('D MMMM YYYY') }}</time>
                    </span>
                    @if ($article->url)
                        <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer" class="press-archive__title">{{ $article->title }}</a>
                    @else
                        <span class="press-archive__title">{{ $article->title }}</span>
                    @endif
                    @if ($article->getFirstMedia('document'))
                        <a href="{{ $article->getFirstMediaUrl('document') }}" target="_blank" rel="noopener noreferrer" class="press-archive__document">{{ __('about.press_document_label') }}</a>
                    @endif
                </li>
            @endforeach
        </ul>
    @endforeach
</div>
