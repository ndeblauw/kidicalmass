@props(['title', 'sub' => null])

{{-- Closing CTA band shared across the About leaves. Default actions route the
     decider forward (rides + help out); pass an `actions` slot to override
     (e.g. a mailto contact on Pers/Partners). Full-bleed yellow, mirrors gs-cta. --}}
<section class="about-cta">
    <div class="container mx-auto px-4 about-cta__inner">
        <h2>{{ $title }}</h2>
        @if ($sub)
            <p class="about-cta__sub">{{ $sub }}</p>
        @endif
        <div class="about-cta__actions">
            @isset($actions)
                {{ $actions }}
            @else
                <a href="{{ route('activities.index') }}" class="about-cta__btn about-cta__btn--primary link-plain">Vind een rit →</a>
                <a href="{{ route('volunteer') }}" class="about-cta__btn about-cta__btn--ghost link-plain">Help mee →</a>
            @endisset
        </div>
    </div>
</section>
