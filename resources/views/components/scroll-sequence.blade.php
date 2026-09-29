@props([
    'mediaSide' => 'right', // right | left — which side the sticky media column sits on (lg+)
    // IntersectionObserver rootMargin for the active-swap band (top right bottom
    // left, % or px). A block takes over the media when its top edge crosses the
    // band's centre line: the default swaps just above 55% of the viewport; move
    // the band up to swap later, once the new block fills the view.
    'activeMargin' => '-50% 0px -40% 0px',
])

{{-- Reusable scrollytelling unit. The text column (default slot) scrolls; the media
     column (`media` slot) is sticky on lg+ and crossfades between its items as each
     [data-seq-block] reaches the active band (see activeMargin). Layout/sticky/crossfade live in
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

            // The block that covers most of the active band drives the sticky media.
            // Picking from every block in the band (not the last one to enter it)
            // makes the swap symmetric: scrolling down or up, a jump from an
            // anchor link, all land on the block the reader is looking at. The
            // swap fires when a block edge crosses the band's centre line.
            if (this.$refs.media) {
                const margin = '{{ $activeMargin }}'.trim().split(/\s+/);
                const inset = (value) => value.endsWith('%') ? -parseFloat(value) / 100 * window.innerHeight : -parseFloat(value);
                const inBand = new Set();
                const pick = () => {
                    const top = inset(margin[0]);
                    const bottom = window.innerHeight - inset(margin[2] ?? margin[0]);
                    let best = null;
                    let bestOverlap = -Infinity;
                    inBand.forEach(block => {
                        const rect = block.getBoundingClientRect();
                        const overlap = Math.min(rect.bottom, bottom) - Math.max(rect.top, top);
                        if (overlap > bestOverlap) { best = block; bestOverlap = overlap; }
                    });
                    if (best) this.setActive(Number(best.dataset.seqBlock) || 0);
                };
                const center = new IntersectionObserver((entries) => {
                    entries.forEach(e => e.isIntersecting ? inBand.add(e.target) : inBand.delete(e.target));
                    pick();
                }, { rootMargin: '{{ $activeMargin }}', threshold: 0 }); // page-tunable band; its centre line is where the swap (and the ride-away) fires
                this.$el.querySelectorAll('[data-seq-block]').forEach(b => center.observe(b));

                // Two blocks share the band only around a hand-over; re-pick on
                // scroll just then, once per frame.
                let queued = false;
                window.addEventListener('scroll', () => {
                    if (inBand.size < 2 || queued) return;
                    queued = true;
                    requestAnimationFrame(() => { queued = false; pick(); });
                }, { passive: true });
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
