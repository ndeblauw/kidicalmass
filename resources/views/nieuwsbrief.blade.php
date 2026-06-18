<x-layouts::site title="Nieuwsbrief">
    {{-- Standard brand-blue hero so the fixed header logo keeps its dark backdrop,
         like every other inner page. Below it, a yellow band: reassurance list on
         the left, the form card on the right pulled up so it dips into the blue. --}}
    <x-page-hero eyebrow="Nieuwsbrief" title="Elke maand de nieuwste ritten in je bus" size="compact" illustration="img/illustrations/relaxed-rider.svg" panelClass="page-panel--newsletter">
        {{-- Benefits stay anchored left (aligned under the hero headline); from lg the
             block widens and the column gap grows so the form card moves right into the
             open space instead of hugging the left edge. --}}
        <div class="grid gap-10 lg:gap-x-16 md:grid-cols-[1fr_1.6fr] items-start max-w-4xl lg:max-w-5xl xl:max-w-6xl ml-0">
            <aside class="md:pt-6">
                <ul class="newsletter-signup-benefits">
                    <li>Eén mail per maand, niet meer</li>
                    <li>Alleen ritten bij jou in de buurt</li>
                    <li>Geen spam, uitschrijven met één klik</li>
                </ul>
            </aside>

            <div class="newsletter-form-col">
                <livewire:newsletter-signup />
            </div>
        </div>
    </x-page-hero>

    {{-- Empty closing fuses the yellow panel with the yellow footer zone (no white gap). --}}
    <x-slot:closing></x-slot:closing>
</x-layouts::site>
