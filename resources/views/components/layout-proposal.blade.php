{{-- Staging-only review marker: wraps a block placed as a best-effort proposal
     because the French content outgrew the current layout. Renders only behind
     config('i18n.show_review_markers') and when the `when` prop is true (pass false
     to keep a shared block unmarked on one locale); when off the wrapped block renders
     as-is. Self-closing use renders just the badge. --}}
@props(['when' => true])
@if ($when && config('i18n.show_review_markers'))
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