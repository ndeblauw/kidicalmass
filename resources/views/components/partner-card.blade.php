@props(['name'])

{{-- Quiet-tier partner card: white, upright, hairline + float shadow (matches
     the article-card register). The slot is an optional short description.
     Styling: components/partner-card.css. --}}
<li {{ $attributes->merge(['class' => 'partner-card']) }}>
    <strong>{{ $name }}</strong>
    @if ($slot->isNotEmpty())
        <p>{{ $slot }}</p>
    @endif
</li>
