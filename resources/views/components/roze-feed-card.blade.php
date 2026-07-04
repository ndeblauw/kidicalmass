@props([
    'color' => 'blue',
    'icon' => 'image', // image | pencil | user-plus | calendar
    'what',
    'context',
    'timestamp', // ISO date for <time datetime>
    'relative',  // human label, e.g. "2 dagen geleden"
    'href',
    'celebrate' => false, // one-time chip pop for feel-good events (new member)
])

@php
    $iconSvg = match ($icon) {
        'pencil' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'user-plus' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        default => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="M21 15l-5-5L5 21"/>',
    };
@endphp

<a href="{{ $href }}" class="roze-feed" @if ($celebrate) data-celebrate @endif>
    <x-icon-chip :color="$color" size="md" :shadow="true">
        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $iconSvg !!}</svg>
    </x-icon-chip>
    <span class="roze-feed__body">
        <span class="roze-feed__what roze-row-title">{{ $what }}</span>
        <span class="roze-feed__meta">{{ $context }} · <time datetime="{{ $timestamp }}">{{ $relative }}</time></span>
    </span>
    <svg class="roze-feed__chev" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
</a>
