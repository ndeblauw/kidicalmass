@props(['href', 'icon', 'title'])

{{-- Hub navigation card: icon chip + title + description (slot) + affordance
     arrow. The hover lift+tilt is an interactive navigation affordance, not
     the decorative card tilt the about pages dropped in the normalize pass.
     Titles are <h3>: cards always sit under a section's <x-section-heading>.
     Styling: components/nav-card.css. --}}
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'nav-card link-plain']) }}>
    <x-icon-chip class="nav-card__chip"><flux:icon name="{{ $icon }}" variant="solid" class="size-7" aria-hidden="true" /></x-icon-chip>
    <h3 class="nav-card__title">{{ $title }}</h3>
    <p class="nav-card__desc">{{ $slot }}</p>
    <span class="nav-card__arrow" aria-hidden="true">→</span>
</a>
