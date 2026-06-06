@props([
    'heading',
    'href',
    'label',
    'icon' => 'arrow', // arrow | heart (heart for membership/support targets)
])

{{-- Page-owned closing block: big heading + one button on a full-bleed yellow band.
     Rendered by the layout's `closing` slot, directly above the footer zone. The
     shared kidical-yellow token + zero gap fuses it with the footer's yellow field.
     Raw <h2> so it inherits the @layer base heading scale (never size headings inline). --}}
<section {{ $attributes->merge(['class' => 'relative z-10 bg-kidical-yellow']) }}>
    <div class="container mx-auto px-4 py-20 flex flex-col items-center gap-7 text-center">
        <h2 class="max-w-3xl">{{ $heading }}</h2>

        <x-cta-button :href="$href" :icon="$icon" variant="blue">{{ $label }}</x-cta-button>
    </div>
</section>
