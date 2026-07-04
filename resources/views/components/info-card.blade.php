@props(['label', 'as' => 'h2'])

{{-- The label is a real heading (default h2) so the card lands in the page
     outline; info-card.css styles it down to the archive-year size. --}}
<div {{ $attributes->merge(['class' => 'info-card']) }}>
    <{{ $as }} class="info-card__label">{{ $label }}</{{ $as }}>
    {{ $slot }}
</div>
