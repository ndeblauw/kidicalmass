@props(['size' => 'base'])

<div {{ $attributes->merge(['class' => 'intro-text'.($size === 'lead' ? ' intro-text--lead' : '')]) }}>
    {{ $slot }}
</div>
