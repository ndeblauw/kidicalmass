<div>
    {{-- HEADER — blue band. The location-first "Waar fiets je?" picker IS the hero
         control: a big yellow searchable select that pops against the blue. --}}
    <section class="index-hero">
        <img src="{{ asset('img/logo-icon.png') }}" alt="" aria-hidden="true" class="index-hero__daisy">

        <div class="container mx-auto px-4 index-hero__inner">
            <h1>Kalender</h1>
            <p class="kal-hero__lead">Vind een fietstocht bij jou in de buurt.</p>

            {{-- LOCATION-FIRST FILTER (Direction A): gemeente is the primary control --}}
            <div class="kal-herofilter">
                <label class="kal-herofilter__label" for="kal-gemeente">Waar fiets je?</label>
                <flux:select
                    id="kal-gemeente"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="gemeente"
                    placeholder="Alle gemeenten"
                    class="kal-herofilter__select"
                >
                    <flux:select.option value="">Alle gemeenten</flux:select.option>
                    @foreach ($gemeenten as $g)
                        <flux:select.option :value="$g->id">{{ $g->zip ? $g->zip.' – '.$g->name : $g->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>
    </section>

    <div class="kal-body">
        <div class="kal-periodbar">
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
                    $periodDate = \Illuminate\Support\Carbon::parse($periodKey)->locale('nl');
                    $landmark = null;
                    if ($when === 'aankomend') {
                        $landmark = $periodDate->isToday() ? 'Vandaag'
                            : ($periodDate->isTomorrow() ? 'Morgen'
                            : (($periodDate->isCurrentWeek() && $periodDate->isWeekend()) ? 'Dit weekend' : null));
                    }
                @endphp
                {{-- Each day is a band: a calendar-page date tile in a narrow left rail, rides on the right. --}}
                <section class="kal-day">
                    @if ($when === 'voorbije')
                        <h2 class="kal-day__date">
                            <time datetime="{{ $periodDate->format('Y-m') }}" class="kal-day__tile kal-day__tile--month">
                                <span class="kal-day__num kal-day__num--month">{{ \Illuminate\Support\Str::ucfirst($periodDate->isoFormat('MMMM')) }}</span>
                                <span class="kal-day__month">{{ $periodDate->isoFormat('YYYY') }}</span>
                            </time>
                        </h2>
                    @else
                        <h2 class="kal-day__date">
                            <time datetime="{{ $periodDate->toDateString() }}" class="kal-day__tile">
                                <span class="kal-day__eyebrow @if ($landmark) kal-day__eyebrow--landmark @endif">{{ $landmark ?? \Illuminate\Support\Str::ucfirst($periodDate->isoFormat('dddd')) }}</span>
                                <span class="kal-day__num">{{ $periodDate->isoFormat('D') }}</span>
                                <span class="kal-day__month">{{ $periodDate->isoFormat('MMMM') }}</span>
                            </time>
                        </h2>
                    @endif
                    <div class="kal-day__cards">
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
</div>
