<x-roze-hub :group="$group" active="materiaal" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;

        // FAUX materials until the per-group materials model lands (Nico / GitHub #37).
        // visibility = the eventual publiek/besloten split: besloten = hesje-only.
        $materials = [
            ['icon' => 'document-text', 'title' => 'Afsprakencharter', 'desc' => 'Onze afspraken voor organisatoren en hesjes.', 'tag' => 'PDF', 'visibility' => 'besloten', 'href' => '#'],
            ['icon' => 'map', 'title' => 'Zo organiseer je een rit', 'desc' => 'Route, gemeentecontact en promo, stap voor stap.', 'tag' => 'Gids', 'visibility' => 'besloten', 'href' => '#'],
            ['icon' => 'megaphone', 'title' => 'De startspeech', 'desc' => 'Het woordje voor de start, voor wie een rit trekt.', 'tag' => 'Voor kapiteins', 'visibility' => 'besloten', 'href' => '#'],
            ['icon' => 'musical-note', 'title' => 'Playlist', 'desc' => 'De muziek voor onderweg, samengesteld door het chapter.', 'tag' => 'Spotify', 'visibility' => 'besloten', 'href' => '#'],
            ['icon' => 'arrow-down-tray', 'title' => 'Posters & promo', 'desc' => 'Affiches en flyers om in je buurt op te hangen.', 'tag' => 'Download', 'visibility' => 'publiek', 'href' => '#'],
            ['icon' => 'arrow-down-tray', 'title' => 'Flyer '.$gemeente.' 2026', 'desc' => 'De lokale flyer om uit te delen.', 'tag' => 'PDF', 'visibility' => 'publiek', 'href' => '#'],
        ];
    @endphp

    {{-- 6 · JOUW MATERIAAL — the chapter's material library (replaces the public CTA).
         FAUX until the backend lands. The startspeech is a besloten "Voor kapiteins" tile. --}}
    {{-- faux: per-chapter playlist URL (Nico #37) --}}
    <section id="jouw-materiaal" class="roze-materials-section scroll-mt-24">
        <h2 class="roze-hub-title">Jouw materiaal</h2>
        <p class="roze-hub-lead">Alles op één plek. <strong>Besloten</strong> blijft bij de hesjes; <strong>publiek</strong> mag je vrij delen.</p>
        <div class="roze-materials">
            @foreach ($materials as $material)
                @php $external = \Illuminate\Support\Str::startsWith($material['href'], 'http'); @endphp
                <a href="{{ $material['href'] }}" @if ($external) target="_blank" rel="noopener" @endif class="roze-material link-plain">
                    <span class="roze-material__icon roze-material__icon--{{ $material['visibility'] }}" aria-hidden="true">
                        <flux:icon name="{{ $material['icon'] }}" variant="solid" class="size-6" />
                    </span>
                    <strong class="roze-material__title roze-card-title">{{ $material['title'] }}</strong>
                    <span class="roze-material__desc">{{ $material['desc'] }}</span>
                    <span class="roze-material__tags">
                        <span class="roze-material__tag">{{ $material['tag'] }}</span>
                        <span class="roze-material__badge roze-material__badge--{{ $material['visibility'] }}">{{ $material['visibility'] === 'besloten' ? 'Besloten' : 'Publiek' }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
</x-roze-hub>
