{{-- Three overlapping envelope chips in the brand colours. A decorative motif
     shared between the home newsletter band and the nieuwsbrief hero. --}}
<span {{ $attributes->class('envelope-chips') }} aria-hidden="true">
    @foreach (['green', 'red', 'blue'] as $tone)
        <span class="envelope-chips__chip envelope-chips__chip--{{ $tone }}">
            <svg viewBox="0 0 24 24" fill="none">
                <rect x="2.75" y="5" width="18.5" height="14" rx="2.5" stroke="currentColor" stroke-width="2"/>
                <path d="M4 7l8 5.5L20 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
    @endforeach
</span>
