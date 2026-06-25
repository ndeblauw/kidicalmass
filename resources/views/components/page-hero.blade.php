@props([
    'eyebrow',
    'title',
    'illustration' => null,
    'photo' => null,
    'photoAlt' => '',
    'caption' => null,
    'size' => 'default',   // 'default' | 'compact' — compact = shorter band + smaller title, for single-action pages (e.g. the newsletter signup)
    'panelClass' => '',
    'photoTilt' => false,  // tilt the photo card and let it dip out of the blue band (chapter-page treatment); opts the hero out of the pinned scroll-over
])

{{-- In-flow brand-blue hero. The band sizes to its content (min-height floor),
     so the heading never clips. .page-panel overlaps its bottom edge; the floating
     nav pill (site header) sits above this. Pass `photo` to swap the floating
     illustration for a rounded photo card beside the title (with an optional
     `caption` credit), the same in-hero treatment the chapter page uses. --}}
<header class="page-hero {{ $photo ? 'page-hero--has-photo' : '' }} {{ $photoTilt ? 'page-hero--photo-tilt' : '' }} {{ $size === 'compact' ? 'page-hero--compact' : '' }}">
    <div class="page-hero__inner container mx-auto px-4">
        <div class="page-hero__copy">
            <p class="page-hero__eyebrow">{{ $eyebrow }}</p>
            <h1 class="page-hero__title">{{ $title }}</h1>
            @isset($lead)
                <div class="page-hero__lead">{{ $lead }}</div>
            @endisset
            @isset($controls)
                <div class="page-hero__controls">{{ $controls }}</div>
            @endisset
        </div>

        @if ($photo)
            <figure class="page-hero__figure">
                <img src="{{ asset($photo) }}" alt="{{ $photoAlt }}" class="page-hero__photo" fetchpriority="high">
                @if ($caption)
                    <figcaption class="page-hero__credit">{{ $caption }}</figcaption>
                @endif
            </figure>
        @endif
    </div>

    @if ($illustration)
        <div class="page-hero__visual" aria-hidden="true">
            <img src="{{ asset($illustration) }}" alt="" class="page-hero__illustration">
        </div>
    @endif
</header>

{{-- Rounded-top panel; overlaps the hero's bottom edge (margin-top: -2rem). White
     by default; pass `panelClass` for a page-scoped surface (e.g. yellow). --}}
<div class="page-panel {{ $panelClass }}">
    <div class="page-panel__inner container mx-auto px-4">
        {{ $slot }}
    </div>
</div>
