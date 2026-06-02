@props(['label' => 'Placeholder', 'note' => null])

{{-- Branded placeholder for sections whose feature/data backing is built later (map, filters,
     routed forms, chapter-managed content). Communicates intended structure on-brand. --}}
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center gap-1 rounded-xl border border-dashed border-kidical-blue/30 bg-kidical-light-yellow/50 p-6 text-center']) }}>
    <span class="font-heading font-extrabold text-kidical-blue">{{ $label }}</span>
    @if ($note)
        <span class="text-sm text-kidical-ink/60">{{ $note }}</span>
    @endif
</div>
