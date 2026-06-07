@props(['name', 'role', 'photo' => null])

<div {{ $attributes->merge(['class' => 'person-card']) }}>
    @if ($photo)
        <img src="{{ $photo }}" alt="{{ $name }}" class="person-card__photo" loading="lazy">
    @endif
    <span class="person-card__name">{{ $name }}</span>
    <span class="person-card__role">{{ $role }}</span>
</div>
