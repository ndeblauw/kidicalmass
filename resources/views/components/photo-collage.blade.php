{{--
    Photo-collage media unit (PAT-20) — standalone/static variant.

    An organic collage of 2–3 snapshots scattered on a square stage at slight
    angles, with an optional brand doodle pinned in a corner and a staggered
    "settle" entrance when the collage scrolls into view. Replaces a single
    framed photo when a section wants more life and warmth.

    Extracted on its second use, per
    docs/superpowers/specs/2026-06-18-photo-collage-media-design.md. The Meehelpen
    page still carries the scroll-sequence-integrated sibling (.ho-deal__collage);
    it can migrate onto this component later.

    Props:
    - photos: array of ['src', 'alt', 'x', 'y', 'w', 'r'] (+ optional 'pos' for
      object-position). x/y = centre point on the stage, w = width, r = resting
      rotation — all as CSS values (%, deg). 2–3 entries read best.
    - doodle: optional illustration path, pinned bottom-left, static by design.
    - reveal: self-driven settle-on-scroll entrance (default true). Set false when
      the collage lives inside a scroll-sequence — there the parent toggles
      `is-active` to crossfade beats, and the photos stay at rest.
--}}
@props([
    'photos' => [],
    'doodle' => null,
    'reveal' => true,
])

<div {{ $attributes->class('photo-collage') }} @if ($reveal) data-photo-collage @endif>
    @foreach ($photos as $i => $photo)
        @php
            // Placement may be passed inline (x/y/w/r/pos) or set in page CSS by
            // nth-child — only emit the custom properties that were provided.
            $placement = [];
            foreach (['x' => '--pc-x', 'y' => '--pc-y', 'w' => '--pc-w', 'r' => '--pc-r', 'pos' => '--pc-pos'] as $key => $var) {
                if (array_key_exists($key, $photo)) {
                    $placement[] = "{$var}: {$photo[$key]}";
                }
            }
            $placement[] = "--pc-i: {$i}";
            $placement[] = 'z-index: '.($i + 1);
        @endphp
        <figure class="photo-collage__photo" style="{{ implode('; ', $placement) }}">
            <img src="{{ asset($photo['src']) }}" alt="{{ $photo['alt'] }}" loading="lazy">
        </figure>
    @endforeach

    @if ($doodle)
        <img class="photo-collage__doodle" src="{{ asset($doodle) }}" alt="" aria-hidden="true">
    @endif
</div>

@once
@push('scripts')
<script>
    {{-- Settle entrance: photos start slightly "tossed" and land one after another
         when the collage scrolls into view. No-JS shows them at rest; reduced
         motion skips arming entirely. --}}
    document.addEventListener('DOMContentLoaded', () => {
        const collages = document.querySelectorAll('[data-photo-collage]');
        if (!collages.length) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        collages.forEach((collage) => collage.classList.add('is-armed'));

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        collages.forEach((collage) => observer.observe(collage));
    });
</script>
@endpush
@endonce
