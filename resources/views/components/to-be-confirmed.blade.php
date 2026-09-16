{{-- Staging-only review marker: wraps a block whose claim the client still has to
     confirm. Renders only behind config('i18n.show_review_markers'); when the flag is
     off the wrapped block renders as-is. Self-closing use renders just the badge. --}}
@if (config('i18n.show_review_markers'))
    <div {{ $attributes->merge(['class' => 'review-marker review-marker--to-be-confirmed']) }}
         data-review-marker="to-be-confirmed">
        <span class="review-marker__label">{{ __('common.review_markers.to-be-confirmed') }}</span>
        @if ($slot->isNotEmpty())
            <div class="review-marker__body">{{ $slot }}</div>
        @endif
    </div>
@else
    {{ $slot }}
@endif