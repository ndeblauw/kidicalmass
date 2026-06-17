@props([
    'mediaSide' => 'right', // right | left — which side the sticky media column sits on (lg+)
])

{{-- Reusable scrollytelling unit. The text column (default slot) scrolls; the media
     column (`media` slot) is sticky on lg+ and crossfades between its items as each
     [data-seq-block] reaches the viewport centre. Layout/sticky/crossfade live in
     resources/css/components/scroll-sequence.css; pages style the media items' own
     look and may override the mobile fallback. Alpine drives the crossfade (the public
     layout ships no global JS, but Alpine is already loaded for other components). --}}
<div
    {{ $attributes->merge(['class' => 'scroll-sequence scroll-sequence--media-'.$mediaSide]) }}
    x-data="{
        setActive(i) {
            this.$refs.media?.querySelectorAll('[data-seq-media]').forEach(el => {
                const idx = Number(el.dataset.seqMedia);
                el.classList.toggle('is-active', idx === i);
                el.classList.toggle('is-past', idx < i); // rode past: lets a page send it out one side
            });
        },
        init() {
            this.$el.classList.add('is-ready'); // lets a page gate JS-driven reveals so copy is never hidden without JS

            // Which block sits at the viewport centre drives the sticky media.
            if (this.$refs.media) {
                const center = new IntersectionObserver((entries) => {
                    entries.forEach(e => {
                        if (e.isIntersecting) this.setActive(Number(e.target.dataset.seqBlock) || 0);
                    });
                }, { rootMargin: '-50% 0px -40% 0px', threshold: 0 }); // band a touch below centre: the active swap (and the ride-away) fires a bit earlier
                this.$el.querySelectorAll('[data-seq-block]').forEach(b => center.observe(b));
            }

            // One-time reveal: stagger a block's contents in as it scrolls into view.
            const reveal = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('is-inview');
                        reveal.unobserve(e.target);
                    }
                });
            }, { rootMargin: '0px 0px -15% 0px', threshold: 0.15 });
            this.$el.querySelectorAll('[data-seq-block]').forEach(b => reveal.observe(b));
        }
    }"
>
    <div class="scroll-sequence__layout">
        <div class="scroll-sequence__media" x-ref="media" aria-hidden="true">
            <div class="scroll-sequence__media-sticky">
                {{ $media }}
            </div>
        </div>
        <div class="scroll-sequence__text">
            {{ $slot }}
        </div>
    </div>
</div>
