@props(['attribution', 'variant' => 'large'])

<figure {{ $attributes->merge(['class' => 'pull-quote pull-quote--'.$variant]) }}>
    <blockquote><p>{{ $slot }}</p></blockquote>
    <figcaption>{{ $attribution }}</figcaption>
</figure>
