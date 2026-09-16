{{-- Staging-only review marker: wraps a block placed as a best-effort proposal
     because the French content outgrew the current layout. Renders only behind
     config('i18n.show_review_markers'); when the flag is off the wrapped block renders
     as-is. Self-closing use renders just the badge. --}}
@if (config('i18n.show_review_markers'))
    <div {{ $attributes->merge(['class' => 'review-marker review-marker--layout-proposal']) }}
         data-review-marker="layout-proposal">
        <span class="review-marker__label">{{ __('common.review_markers.layout-proposal') }}</span>
        @if ($slot->isNotEmpty())
            <div class="review-marker__body">{{ $slot }}</div>
        @endif
    </div>
@else
    {{ $slot }}
@endif