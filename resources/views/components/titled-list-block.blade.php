@props(['title'])

<div {{ $attributes->merge(['class' => 'titled-list-block']) }}>
    <h3 class="titled-list-block__title">{{ $title }}</h3>
    <ul class="titled-list-block__list" role="list">
        {{ $slot }}
    </ul>
</div>
