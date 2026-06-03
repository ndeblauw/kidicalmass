<div>
    {{-- LOCATION-FIRST FILTER (Direction A): gemeente is the primary control --}}
    <div class="kal-filterbar">
        <div class="kal-locationfield">
            <label class="kal-locationlabel" for="kal-gemeente">Waar fiets je?</label>
            <flux:select
                id="kal-gemeente"
                variant="listbox"
                searchable
                clearable
                wire:model.live="gemeente"
                placeholder="Alle gemeenten"
                class="kal-locationselect"
            >
                <flux:select.option value="">Alle gemeenten</flux:select.option>
                @foreach ($gemeenten as $g)
                    <flux:select.option :value="$g->id">{{ $g->zip ? $g->zip.' – '.$g->name : $g->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        @if ($when === 'aankomend')
            <button type="button" wire:click="showPast" class="kal-pastlink">Bekijk voorbije ritten →</button>
        @else
            <button type="button" wire:click="showUpcoming" class="kal-pastlink">← Terug naar aankomende ritten</button>
        @endif
    </div>

    @if ($hasActivities)
        <div class="kal-days">
            @foreach ($byPeriod as $periodKey => $periodActivities)
                @php
                    $periodDate = \Illuminate\Support\Carbon::parse($periodKey);
                    $dateLabel = \Illuminate\Support\Str::ucfirst($periodDate->locale('nl')->isoFormat($headerFormat));
                    $landmark = null;
                    if ($when === 'aankomend') {
                        $landmark = $periodDate->isToday() ? 'Vandaag'
                            : ($periodDate->isTomorrow() ? 'Morgen'
                            : (($periodDate->isCurrentWeek() && $periodDate->isWeekend()) ? 'Dit weekend' : null));
                    }
                @endphp
                <section class="space-y-4">
                    <h2 class="kal-day__title">
                        @if ($landmark)
                            <span class="kal-day__landmark">{{ $landmark }}</span><span class="kal-day__sub">{{ $dateLabel }}</span>
                        @else
                            {{ $dateLabel }}
                        @endif
                    </h2>
                    <div class="space-y-3">
                        @foreach ($periodActivities as $activity)
                            <x-event-card :activity="$activity" :show-date="false" />
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @else
        <p class="kal-empty">
            @if ($when === 'voorbije')
                Er zijn nog geen voorbije fietstochten om te tonen.
            @elseif ($selectedGemeente)
                Geen aankomende fietstochten in {{ $selectedGemeente }}. Kies 'Alle gemeenten' voor fietstochten in de buurt.
            @else
                Er zijn momenteel geen fietstochten gepland. Het seizoen loopt van maart tot november. Kom snel terug!
            @endif
        </p>
    @endif
</div>
