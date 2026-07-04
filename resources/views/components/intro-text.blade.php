@props(['size' => 'base'])

{{-- Page intro block. size="lead" is the manifesto variant: heavier, ink-dark
     and a scale up — deliberately reserved for the Wat we vragen (vision)
     position statement; every other intro uses the base size. --}}
<div {{ $attributes->merge(['class' => 'intro-text'.($size === 'lead' ? ' intro-text--lead' : '')]) }}>
    {{ $slot }}
</div>
