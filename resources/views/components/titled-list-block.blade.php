@props([
    'title',
    'variant' => null, // null (quiet dot) | get (green check) | ask (red chevron)
    'level' => 'h3',  // semantic heading level: h2 when it's a top-level section, h3 under an h2
])

@php
    $classes = collect([
        'titled-list-block',
        $variant ? 'titled-list-block--'.$variant : null,
    ])->filter()->implode(' ');
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    <{{ $level }} class="titled-list-block__title">{{ $title }}</{{ $level }}>
    <ul class="titled-list-block__list" role="list">
        {{ $slot }}
    </ul>
</div>
