@props(['label'])

<div {{ $attributes->merge(['class' => 'info-card']) }}>
    <span class="info-card__label">{{ $label }}</span>
    {{ $slot }}
</div>
