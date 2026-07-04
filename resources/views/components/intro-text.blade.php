@props(['size' => 'base'])

{{-- Page intro block. Inner pages use the base size — the bold size="lead"
     variant is reserved for the homepage pitch (it read as a bold wall on
     vision and was dropped there, 2026-07-04). --}}
<div {{ $attributes->merge(['class' => 'intro-text'.($size === 'lead' ? ' intro-text--lead' : '')]) }}>
    {{ $slot }}
</div>
