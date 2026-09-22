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
                'title' => __('roze.materiaal.sections.hesjes.title'),
                'visibility' => 'besloten',
                'items' => [
                    ['icon' => 'document-text', 'title' => __('roze.materiaal.sections.hesjes.items.charter.title'), 'desc' => __('roze.materiaal.sections.hesjes.items.charter.desc'), 'tag' => __('roze.materiaal.sections.hesjes.items.charter.tag'), 'href' => '#'],
                    ['icon' => 'map', 'title' => __('roze.materiaal.sections.hesjes.items.howto.title'), 'desc' => __('roze.materiaal.sections.hesjes.items.howto.desc'), 'tag' => __('roze.materiaal.sections.hesjes.items.howto.tag'), 'href' => '#'],
                    ['icon' => 'megaphone', 'title' => __('roze.materiaal.sections.hesjes.items.speech.title'), 'desc' => __('roze.materiaal.sections.hesjes.items.speech.desc'), 'tag' => __('roze.materiaal.sections.hesjes.items.speech.tag'), 'href' => '#'],
                ],
            ],
            [
                'id' => 'vrij-om-te-delen',
                'title' => __('roze.materiaal.sections.public.title'),
                'visibility' => 'publiek',
                'items' => [
                    ['icon' => 'musical-note', 'title' => __('roze.materiaal.sections.public.items.playlist.title'), 'desc' => __('roze.materiaal.sections.public.items.playlist.desc'), 'tag' => __('roze.materiaal.sections.public.items.playlist.tag'), 'href' => '#'],
                    ['icon' => 'arrow-down-tray', 'title' => __('roze.materiaal.sections.public.items.promo.title'), 'desc' => __('roze.materiaal.sections.public.items.promo.desc'), 'tag' => __('roze.materiaal.sections.public.items.promo.tag'), 'href' => '#'],
                    ['icon' => 'arrow-down-tray', 'title' => __('roze.materiaal.sections.public.items.flyer.title', ['place' => $gemeente]), 'desc' => __('roze.materiaal.sections.public.items.flyer.desc'), 'tag' => __('roze.materiaal.sections.public.items.flyer.tag'), 'href' => '#'],
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
    <h1 class="roze-hub-title">{{ __('roze.materiaal.title') }}</h1>
    @if ($hasSoon)
        <p class="roze-materials__note">{!! __('roze.materiaal.note') !!}</p>
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
                                    <span class="roze-material__soon">{{ __('roze.materiaal.soon') }}</span>
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
