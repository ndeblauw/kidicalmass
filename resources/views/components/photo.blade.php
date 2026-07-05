@props([
    'src',                                   // path under public/, e.g. "img/photography/foo.webp"
    'alt' => null,                           // required — pass alt="" only for genuinely decorative photos
    'sizes' => '(min-width: 768px) 45vw, 92vw',
    'loading' => 'lazy',
    'fetchpriority' => null,
])

{{-- Responsive content photo. When a pre-generated 768px-wide sibling
     ({name}-768.webp) exists and the original is wider than 768px, emit a
     two-step srcset so phones fetch the small variant instead of the full
     image. Placement/appearance is owned by the surrounding figure/CSS;
     extra attributes (class, style, …) pass through. --}}
@php
    if ($alt === null) {
        throw new \InvalidArgumentException(
            "<x-photo src=\"{$src}\"> needs an alt decision: describe the photo, or pass alt=\"\" if it is genuinely decorative."
        );
    }

    $srcset = null;

    if (str_ends_with($src, '.webp')) {
        $variant = substr($src, 0, -strlen('.webp')).'-768.webp';
        $variantAbs = public_path($variant);

        if (is_file($variantAbs)) {
            $fullAbs = public_path($src);
            $width = \Illuminate\Support\Facades\Cache::rememberForever(
                'photo-width:'.$src.':'.(@filemtime($fullAbs) ?: 0),
                fn () => @getimagesize($fullAbs)[0] ?? null,
            );

            if ($width && $width > 768) {
                $srcset = asset($variant).' 768w, '.asset($src).' '.$width.'w';
            }
        }
    }
@endphp

<img
    src="{{ asset($src) }}"
    @if ($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
    alt="{{ $alt }}"
    loading="{{ $loading }}"
    decoding="async"
    @if ($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
    {{ $attributes }}
>
