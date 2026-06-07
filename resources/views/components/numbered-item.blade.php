@props(['number', 'title'])

<div {{ $attributes->merge(['class' => 'numbered-item']) }}>
    <span class="numbered-item__num" aria-hidden="true">{{ $number }}</span>
    <strong>{{ $title }}</strong>
    <p>{{ $slot }}</p>
</div>
