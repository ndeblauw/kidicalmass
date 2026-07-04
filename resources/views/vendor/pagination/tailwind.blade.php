{{-- Branded paginator (overrides Laravel's stock tailwind view, the default
     for $paginator->links()). Words: lang/nl/common.php; look:
     resources/css/components/pagination.css. --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('common.pagination_label') }}" class="pagination" data-pagination>
        @if ($paginator->onFirstPage())
            <span class="pagination__step pagination__step--disabled" aria-disabled="true">← {{ __('common.pagination_previous') }}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination__step link-plain">← {{ __('common.pagination_previous') }}</a>
        @endif

        <ul class="pagination__pages" role="list">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="pagination__gap" aria-hidden="true">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span class="pagination__page pagination__page--current" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pagination__page link-plain">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination__step link-plain">{{ __('common.pagination_next') }} →</a>
        @else
            <span class="pagination__step pagination__step--disabled" aria-disabled="true">{{ __('common.pagination_next') }} →</span>
        @endif
    </nav>
@endif
