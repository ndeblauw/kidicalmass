@props(['attribution', 'variant' => 'large'])

@php
    /* Marker: the CSS supplies the big red quote mark, so quote marks at the
       edges of the copy (lang fallback or admin-entered) are stripped here;
       the attribution splits on commas into a name plus detail parts that the
       CSS joins with red middots. */
    $isMarker = $variant === 'marker';
    $attributionParts = $isMarker ? explode(', ', $attribution) : [$attribution];
    $attributionName = array_shift($attributionParts);
    $quoteHtml = $isMarker
        ? preg_replace('/^\s*[„“”"]+|[„“”"]+\s*$/u', '', trim($slot->toHtml()))
        : $slot;
@endphp

<figure {{ $attributes->merge(['class' => 'pull-quote pull-quote--'.$variant]) }}>
    <blockquote><p>{!! $quoteHtml !!}</p></blockquote>
    <figcaption>
        @if ($isMarker && $attributionParts !== [])
            <strong class="pull-quote__name">{{ $attributionName }}</strong>@foreach ($attributionParts as $part)<span class="pull-quote__sep" aria-hidden="true">·</span><span class="pull-quote__detail">{{ $part }}</span>@endforeach
        @else
            {{ $attribution }}
        @endif
    </figcaption>
</figure>
