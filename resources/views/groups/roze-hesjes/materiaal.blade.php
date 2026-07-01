<x-roze-hub :group="$group" active="materiaal" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl" :own-heading="true">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;

        // FAUX materials until the per-group materials model lands (Nico / GitHub #37).
        // Two plain groups instead of per-tile badges: besloten blijft bij de hesjes,
        // publiek mag vrij gedeeld worden. The section title carries that split, so the
        // tiles themselves stay quiet. Playlist hoort bij publiek — vrij om te delen.
        $sections = [
            [
                'id' => 'voor-de-hesjes',
                'title' => 'Voor de hesjes',
                'visibility' => 'besloten',
                'items' => [
                    ['icon' => 'document-text', 'title' => 'Afsprakencharter', 'desc' => 'Onze afspraken voor organisatoren en hesjes.', 'tag' => 'PDF', 'href' => '#'],
                    ['icon' => 'map', 'title' => 'Zo organiseer je een rit', 'desc' => 'Route, gemeentecontact en promo, stap voor stap.', 'tag' => 'Gids', 'href' => '#'],
                    ['icon' => 'megaphone', 'title' => 'De startspeech', 'desc' => 'Het woordje voor de start, voor wie een rit trekt.', 'tag' => 'Voor kapiteins', 'href' => '#'],
                ],
            ],
            [
                'id' => 'vrij-om-te-delen',
                'title' => 'Vrij om te delen',
                'visibility' => 'publiek',
                'items' => [
                    ['icon' => 'musical-note', 'title' => 'Playlist', 'desc' => 'De muziek voor onderweg, samengesteld door de groep.', 'tag' => 'Spotify', 'href' => '#'],
                    ['icon' => 'arrow-down-tray', 'title' => 'Posters & promo', 'desc' => 'Affiches en flyers om in je buurt op te hangen.', 'tag' => 'Download', 'href' => '#'],
                    ['icon' => 'arrow-down-tray', 'title' => 'Flyer '.$gemeente.' 2026', 'desc' => 'De lokale flyer om uit te delen.', 'tag' => 'PDF', 'href' => '#'],
                ],
            ],
        ];

        // Whether anything is still pending, so the page can say so up front instead of
        // looking like a ready library where every tile happens to be "Binnenkort".
        $hasSoon = collect($sections)
            ->flatMap(fn ($section) => $section['items'])
            ->contains(fn ($item) => blank($item['href']) || $item['href'] === '#');
    @endphp

    {{-- 6 · JOUW MATERIAAL — the chapter's material library, split into a besloten group
         (voor de hesjes) and a publieke group (vrij om te delen). FAUX until Nico #37. --}}
    {{-- faux: per-chapter playlist URL (Nico #37) --}}
    <h1 class="roze-hub-title">Jouw materiaal</h1>
    @if ($hasSoon)
        <p class="roze-materials__note">We vullen deze map de komende weken verder aan. Alles met een <strong>Binnenkort</strong>-label komt er nog aan.</p>
    @endif

    <div class="roze-materials-page">
        @foreach ($sections as $section)
            <section id="{{ $section['id'] }}" class="roze-materials-section scroll-mt-24">
                <h2 class="roze-hub-subtitle">{{ $section['title'] }}</h2>
                <div class="roze-materials">
                    @foreach ($section['items'] as $material)
                        @php
                            // A material is live once it has a real href; until then (Nico #37) the
                            // tile is an honest preview, not a clickable download that goes nowhere.
                            $available = filled($material['href']) && $material['href'] !== '#';
                            $external = $available && \Illuminate\Support\Str::startsWith($material['href'], 'http');
                        @endphp
                        <a
                            @if ($available) href="{{ $material['href'] }}" @if ($external) target="_blank" rel="noopener" @endif @endif
                            @class(['roze-material link-plain', 'roze-material--soon' => ! $available])
                        >
                            <span class="roze-material__icon roze-material__icon--{{ $section['visibility'] }}" aria-hidden="true">
                                <flux:icon name="{{ $material['icon'] }}" variant="solid" class="size-6" />
                            </span>
                            <h3 class="roze-material__title roze-card-title">{{ $material['title'] }}</h3>
                            <span class="roze-material__desc">{{ $material['desc'] }}</span>
                            <span class="roze-material__tags">
                                @unless ($available)
                                    <span class="roze-material__soon">Binnenkort</span>
                                @endunless
                                <span class="roze-material__tag">{{ $material['tag'] }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</x-roze-hub>
