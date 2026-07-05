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
     image. Intrinsic width/height are emitted so the browser reserves the
     aspect ratio before the file arrives (no layout shift); CSS still owns
     the rendered size. Placement/appearance is owned by the surrounding
     figure/CSS, except the hairline edge outline (components/photo.css via
     the `photo` class); extra attributes (class, style, …) pass through. --}}
@php
    if ($alt === null) {
        throw new \InvalidArgumentException(
            "<x-photo src=\"{$src}\"> needs an alt decision: describe the photo, or pass alt=\"\" if it is genuinely decorative."
        );
    }

    $fullAbs = public_path($src);

    /** @var array{0: int, 1: int}|null $dims */
    $dims = is_file($fullAbs)
        ? \Illuminate\Support\Facades\Cache::rememberForever(
            'photo-dims:'.$src.':'.(@filemtime($fullAbs) ?: 0),
            function () use ($fullAbs): ?array {
                $size = @getimagesize($fullAbs);

                return $size ? [$size[0], $size[1]] : null;
            },
        )
        : null;

    $srcset = null;

    if ($dims && $dims[0] > 768 && str_ends_with($src, '.webp')) {
        $variant = substr($src, 0, -strlen('.webp')).'-768.webp';

        if (is_file(public_path($variant))) {
            $srcset = asset($variant).' 768w, '.asset($src).' '.$dims[0].'w';
        }
    }
@endphp

<img
    src="{{ asset($src) }}"
    @if ($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
    @if ($dims) width="{{ $dims[0] }}" height="{{ $dims[1] }}" @endif
    alt="{{ $alt }}"
    loading="{{ $loading }}"
    decoding="async"
    @if ($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
    {{ $attributes->class(['photo']) }}
>
