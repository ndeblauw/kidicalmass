@props(['title', 'sub' => null])

{{-- Closing CTA — compact floating card that overlaps the partner strip below.
     Pass an `actions` slot to override the default pair (e.g. mailto on Pers/Partners). --}}
<section class="about-cta">
    <figure class="about-cta__visual" aria-hidden="true">
        <img src="{{ asset('img/illustrations/kid-waving.png') }}" alt="">
    </figure>
    <div class="about-cta__content">
        <h2>{{ $title }}</h2>
        @if ($sub)
            <p class="about-cta__sub">{{ $sub }}</p>
        @endif
        <div class="about-cta__actions">
            @isset($actions)
                {{ $actions }}
            @else
                <x-cta-button :href="route('volunteer')" variant="blue" class="link-plain">Help mee</x-cta-button>
                <a href="{{ route('activities.index') }}" class="about-cta__btn about-cta__btn--ink link-plain">
                    Vind een rit
                    <span class="about-cta__btn__disc" aria-hidden="true">→</span>
                </a>
            @endisset
        </div>
    </div>
</section>
