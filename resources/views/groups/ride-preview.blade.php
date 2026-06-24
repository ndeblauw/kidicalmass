<x-layouts::site title="Rit in voorbereiding — Kidical Mass {{ $group->name }}">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;
    @endphp

    {{-- FAUX exemplar draft ride. Replace with a real draft Activity once lifecycle state
         lands (Nico #37). The status line is read-only for hesjes, the captains' working line. --}}
    <section class="chapter-body roze-preview">
        <a href="{{ route('groups.roze-hesjes', $group) }}" class="roze-preview__back link-plain">← Terug naar {{ $gemeente }}</a>

        <p class="roze-preview__flag">Nog niet vast</p>
        <h1>Een rit door {{ $gemeente }}</h1>
        <p class="roze-preview__when">Mogelijk <time datetime="2026-07-12">zondag 12 juli</time>, datum nog te bevestigen.</p>

        <div class="roze-preview__status">
            <strong class="roze-preview__status-title roze-card-title">Wat moet er nog gebeuren</strong>
            <p class="roze-preview__status-body">De route is gekozen, maar de communicatiekaart is nog niet ingevuld. Zodra die klaar is, kondigen de kapiteins de rit aan.</p>
        </div>

        <p class="roze-preview__foot">Je kijkt hier mee terwijl een kapitein deze rit voorbereidt. Benieuwd hoe dat werkt? Vraag het gerust in de groep.</p>
    </section>
</x-layouts::site>
