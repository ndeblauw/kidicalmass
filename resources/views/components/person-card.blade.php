@props(['name', 'role', 'photo' => null, 'bio' => null])

<div {{ $attributes->merge(['class' => 'person-card person-card--row']) }}>
    @if ($photo)
        <img src="{{ $photo }}" alt="{{ $name }}" class="person-card__photo" loading="lazy">
    @else
        <span class="person-card__disc" aria-hidden="true">{{ mb_substr($name, 0, 1) }}</span>
    @endif
    <div class="person-card__text">
        <span class="person-card__name">{{ $name }}</span>
        <span class="person-card__role">{{ $role }}</span>
        @if ($bio)
            <p class="person-card__bio">{{ $bio }}</p>
        @endif
    </div>
</div>
