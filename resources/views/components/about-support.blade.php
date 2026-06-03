@props(['title', 'body'])

{{-- Contextual Steun ask, placed at a peak-intent moment on an About leaf (PAT-10
     spirit, but About-scoped so it can sit on a contrasting ground and carry
     page-specific copy). Reuses the exact site-wide yellow Steun pill
     (.support-callout__cta) for consistency. Always → route('membership'). --}}
<section class="about-support">
    <div class="about-support__inner">
        <div>
            <h2 class="about-support__title">{{ $title }}</h2>
            <p class="about-support__body">{{ $body }}</p>
        </div>
        <a href="{{ route('membership') }}" class="support-callout__cta">
            <flux:icon.heart variant="solid" class="support-callout__cta-icon" aria-hidden="true" />
            {{ __('support.cta') }}
        </a>
    </div>
</section>
