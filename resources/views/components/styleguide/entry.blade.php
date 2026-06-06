@props([
    'name',        // component name, e.g. "cta-button"
    'props' => '', // short props summary
    'note' => null,
])

<div class="sg-section flex flex-col gap-4">
    <div class="flex flex-col gap-1">
        <h3>{{ $name }}</h3>
        @if ($props !== '')
            <p class="text-sm text-kidical-ink/60"><code class="text-xs">{{ $props }}</code></p>
        @endif
        @if ($note)
            <p class="text-sm text-kidical-orange">{{ $note }}</p>
        @endif
    </div>

    @if (! $slot->isEmpty())
        <div class="sg-demo p-8">
            {{ $slot }}
        </div>
    @endif
</div>
