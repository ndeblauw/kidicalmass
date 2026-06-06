@props([
    'token', // CSS var name without var(), e.g. "kidical-blue"
    'name',  // human label
])

<div class="flex flex-col gap-2">
    <div class="h-16 rounded-card border border-kidical-ink/10" style="background: var(--color-{{ $token }})"></div>
    <div class="text-sm">
        <p class="font-semibold">{{ $name }}</p>
        <code class="text-xs text-kidical-ink/60">--color-{{ $token }}</code>
    </div>
</div>
