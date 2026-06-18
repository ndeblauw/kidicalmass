@props([
    'heading' => 'Krijg de nieuwste ritten in je mailbox',
    'lead' => 'Schrijf je in voor de maandelijkse mail. Zo weet je als eerste waar en wanneer er bij jou in de buurt gefietst wordt.',
])

{{-- Page-owned closing block: newsletter sign-up on the full-bleed yellow band.
     Same band treatment as <x-closing-cta> (kidical-yellow + relative z-10) so it
     fuses with the footer's yellow field. The band has a rounded shoulder that rises
     into the white section above; a little fleet of three envelope chips (green/red/
     blue) straddles that seam and gently settles in when the band scrolls into view.
     Reveal is gated on is-ready (added by Alpine) so the chips are never hidden
     without JS, and neutralised under prefers-reduced-motion — see newsletter-cta.css.
     The CTA links to the dedicated signup page (newsletter.show); the form itself
     lives there. Raw <h2> inherits the @layer base heading scale. --}}
<section
    {{ $attributes->merge(['class' => 'newsletter-cta relative z-10 bg-kidical-yellow']) }}
    x-data="{
        init() {
            this.$el.classList.add('is-ready');
            const io = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    if (e.isIntersecting) {
                        this.$el.classList.add('is-inview');
                        io.disconnect();
                    }
                });
            }, { threshold: 0.25 });
            io.observe(this.$el);
        }
    }"
>
    <span class="newsletter-cta__chips" aria-hidden="true">
        @foreach (['green', 'red', 'blue'] as $tone)
            <span class="newsletter-cta__chip newsletter-cta__chip--{{ $tone }}">
                <svg viewBox="0 0 24 24" fill="none">
                    <rect x="2.75" y="5" width="18.5" height="14" rx="2.5" stroke="currentColor" stroke-width="2"/>
                    <path d="M4 7l8 5.5L20 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        @endforeach
    </span>

    <div class="container mx-auto px-4 py-20 flex flex-col items-center gap-6 text-center">
        <h2 class="max-w-3xl mt-12">{{ $heading }}</h2>

        <p class="newsletter-cta__lead max-w-xl">{{ $lead }}</p>

        <x-cta-button :href="route('newsletter.show', ['locale' => app()->getLocale()])" variant="blue" icon="arrow">Schrijf me in</x-cta-button>
    </div>
</section>
