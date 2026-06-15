<div>
    {{-- Header --}}
    <section class="bg-kidical-blue text-white">
        <div class="container mx-auto px-4 py-12 md:py-14 max-w-4xl">
            <p class="text-base font-bold uppercase tracking-[0.12em] text-white/70 mb-3">Foto's uploaden</p>
            <h1 class="text-white -rotate-1 origin-left mb-4">{{ $activity->title_nl }}</h1>
            <p class="text-xl text-white/85 max-w-2xl">
                Voeg foto's toe aan deze activiteit. Maximaal 15 MB per bestand.
            </p>
        </div>
    </section>

    {{-- Activity info + main photo --}}
    <section class="bg-white">
        <div class="container mx-auto px-4 py-14 md:py-16 max-w-3xl">
            <div class="flex flex-col sm:flex-row gap-8 items-start">
                <div class="flex-1 min-w-0">
                    <h2 class="mb-4">Activiteit</h2>
                    <dl class="space-y-3 text-kidical-ink/85">
                        <div class="flex items-center gap-3">
                            <flux:icon name="calendar" variant="solid" class="size-5 text-kidical-blue shrink-0" aria-hidden="true" />
                            <div>
                                <dt class="sr-only">Datum</dt>
                                <dd>{{ ucfirst($activity->begin_date->translatedFormat('l j F Y')) }}</dd>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <flux:icon name="clock" variant="solid" class="size-5 text-kidical-blue shrink-0" aria-hidden="true" />
                            <div>
                                <dt class="sr-only">Tijd</dt>
                                <dd>{{ $activity->begin_date->format('H:i') }}@if ($activity->duration_minutes) – {{ $activity->end_date->format('H:i') }}@endif</dd>
                            </div>
                        </div>
                        @if ($activity->location)
                            <div class="flex items-center gap-3">
                                <flux:icon name="map-pin" variant="solid" class="size-5 text-kidical-blue shrink-0" aria-hidden="true" />
                                <div>
                                    <dt class="sr-only">Locatie</dt>
                                    <dd>{{ $activity->location }}@if ($activity->postal_code), {{ $activity->postal_code }}@endif</dd>
                                </div>
                            </div>
                        @endif
                        @if ($activity->distance)
                            <div class="flex items-center gap-3">
                                <flux:icon name="arrows-right-left" variant="solid" class="size-5 text-kidical-blue shrink-0" aria-hidden="true" />
                                <div>
                                    <dt class="sr-only">Afstand</dt>
                                    <dd>{{ $activity->distance }} km</dd>
                                </div>
                            </div>
                        @endif
                    </dl>
                </div>

                @php $main = $activity->getFirstMedia('main'); @endphp
                @if ($main)
                    <div class="shrink-0 w-full sm:w-48">
                        <img src="{{ $main->getUrl('card') }}" alt="Hoofdfoto" class="w-full rounded-xl shadow-md">
                    </div>
                @endif
            </div>

            @if (! $main)
                <div class="mt-8 bg-kidical-light-yellow/60 rounded-card p-7 shadow-card">
                    <label class="block mb-2 text-sm font-bold text-kidical-ink/70">Hoofdfoto toevoegen</label>
                    <input type="file" wire:model="mainPhoto" accept="image/*"
                           class="block w-full text-sm text-kidical-ink file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-kidical-blue file:text-white hover:file:brightness-105">
                    @error('mainPhoto') <p class="text-kidical-red text-sm mt-2">{{ $message }}</p> @enderror
                    <div wire:loading wire:target="mainPhoto" class="text-kidical-blue text-sm mt-2">Bezig met uploaden...</div>
                </div>
            @endif
        </div>
    </section>

    {{-- Gallery images --}}
    <section class="bg-kidical-light-yellow/30">
        <div class="container mx-auto px-4 py-14 md:py-16 max-w-3xl">
            <h2 class="mb-6">Extra foto's</h2>

            @php $gallery = $activity->getMedia('gallery'); @endphp

            @if ($gallery->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-8">
                    @foreach ($gallery as $media)
                        <div class="relative group bg-white rounded-xl overflow-hidden shadow-card">
                            <img src="{{ $media->getUrl() }}" alt="{{ $media->name }}" class="w-full h-32 object-cover">
                            <div class="p-2 flex items-center justify-between">
                                <span class="text-xs text-kidical-ink/60 truncate">{{ $media->name }}</span>
                                @if ($isCaptain)
                                    <button type="button" wire:click="removeGalleryItem({{ $media->id }})" wire:confirm="Deze foto verwijderen?"
                                            class="shrink-0 size-7 flex items-center justify-center rounded-full bg-kidical-red/10 text-kidical-red hover:bg-kidical-red/20">
                                        <flux:icon name="x-mark" variant="micro" class="size-4" aria-hidden="true" />
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-kidical-ink/60 mb-6">Nog geen extra foto's.</p>
            @endif

            @if (! $isCaptain && $gallery->isNotEmpty())
                <p class="text-sm text-kidical-ink/60 mb-6">
                    Wil je een foto laten verwijderen? Vraag het aan je captain.
                </p>
            @endif

            <div class="bg-white rounded-card p-7 shadow-card">
                <label class="block mb-2 text-sm font-bold text-kidical-ink/70">Foto's toevoegen</label>
                <input type="file" wire:model="galleryPhotos" accept="image/*" multiple
                       class="block w-full text-sm text-kidical-ink file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-kidical-blue file:text-white hover:file:brightness-105">
                @error('galleryPhotos.*') <p class="text-kidical-red text-sm mt-2">{{ $message }}</p> @enderror
                <div wire:loading wire:target="galleryPhotos" class="text-kidical-blue text-sm mt-2">Bezig met uploaden...</div>

                <button type="button" wire:click="uploadGallery"
                        class="mt-4 inline-flex items-center gap-2 bg-kidical-green text-white font-bold rounded-full px-5 py-2.5 hover:brightness-105">
                    <flux:icon name="arrow-up-tray" variant="solid" class="size-5" aria-hidden="true" />
                    Voeg geselecteerde foto's toe aan activiteit
                </button>
            </div>
        </div>
    </section>

    {{-- Back link --}}
    <section class="bg-white">
        <div class="container mx-auto px-4 py-8 max-w-3xl">
            <a href="{{ route('backstage.home', $group) }}" class="inline-flex items-center gap-2 font-bold text-kidical-blue">
                <flux:icon name="arrow-left" variant="micro" class="size-4" aria-hidden="true" />
                Terug naar overzicht
            </a>
        </div>
    </section>
</div>
