@props(['href', 'label'])

{{-- Intent card: a light-blue routing pill for "what are you here for?"
     strips. Hover lift+tilt is a navigation affordance (kept).
     Styling: components/intent-card.css. --}}
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'intent-card link-plain']) }}>
    <span class="intent-card__label">{{ $label }}</span>
    <span class="intent-card__arrow" aria-hidden="true">→</span>
</a>
