<div>
    <x-page-hero
        eyebrow="Kalender"
        title="Spring op de fiets, wij rijden samen."
        illustration="img/illustrations/kid-on-bike.png">

        <x-slot:controls>
            <div class="kal-herofilter">
                <livewire:location-picker />
            </div>
        </x-slot:controls>

        <div class="kal-body">
            <div class="kal-periodbar">
                @if ($when === 'aankomend')
                    <button type="button" wire:click="showPast" class="kal-pastlink">Bekijk voorbije ritten →</button>
                @else
                    <button type="button" wire:click="showUpcoming" class="kal-pastlink">← Terug naar aankomende ritten</button>
                @endif
            </div>

            @if (! $hasActivities)
                <p class="kal-empty">
                    @if ($when === 'voorbije')
                        Er zijn nog geen voorbije fietstochten om te tonen.
                    @else
                        Er zijn momenteel geen fietstochten gepland. Het seizoen loopt van maart tot november. Kom snel terug!
                    @endif
                </p>
            @elseif ($when === 'voorbije')
                <div class="kal-days">
                    @foreach ($byPeriod as $periodKey => $rides)
                        <x-kal-month-band :period-key="$periodKey" :rides="$rides" />
                    @endforeach
                </div>
            @elseif ($location)
                @if ($isEmpty)
                    <p class="kal-empty">Geen fietstochten gevonden in dit gebied. Probeer een groter zoekgebied.</p>
                @elseif ($byPeriod->isNotEmpty())
                    <div class="kal-days">
                        @foreach ($byPeriod as $periodKey => $rows)
                            <x-kal-day-band :period-key="$periodKey" :rows="$rows" />
                        @endforeach
                    </div>
                @endif
            @else
                <div class="kal-days">
                    @foreach ($byPeriod as $periodKey => $rides)
                        <x-kal-day-band :period-key="$periodKey" :rows="$rides" />
                    @endforeach
                </div>
            @endif
        </div>
    </x-page-hero>
</div>
