<x-layouts::site title="Rit in voorbereiding — Kidical Mass {{ $group->name }}">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;

        $title = $ride?->title ?? "Een rit door {$gemeente}";
        $when = $ride
            ? 'Mogelijk ' . $ride->date_full . ', datum nog te bevestigen.'
            : 'Mogelijk zondag 12 juli, datum nog te bevestigen.';
        $whenIso = $ride?->begin_date?->toDateString() ?? '2026-07-12';
    @endphp

    {{-- Read-only preview of a draft ride. Shows a real unpublished Activity when one is
         passed (?ride=), else a faux exemplar. The status line stays prose — there is no
         Activity status field yet (Nico #37) — and is read-only for hesjes. --}}
    <section class="chapter-body roze-preview">
        <a href="{{ route('groups.roze-hesjes.agenda', $group) }}" class="roze-preview__back link-plain">&larr; Terug naar de agenda</a>

        <p class="roze-preview__flag">Nog niet vast</p>
        <h1>{{ $title }}</h1>
        <p class="roze-preview__when"><time datetime="{{ $whenIso }}">{{ $when }}</time></p>

        <div class="roze-preview__status">
            <strong class="roze-preview__status-title roze-card-title">Wat moet er nog gebeuren</strong>
            <p class="roze-preview__status-body">De route is gekozen, maar de communicatiekaart is nog niet ingevuld. Zodra die klaar is, kondigen de kapiteins de rit aan.</p>
        </div>

        <p class="roze-preview__foot">Je kijkt hier mee terwijl een kapitein deze rit voorbereidt. Benieuwd hoe dat werkt? Vraag het gerust in de groep.</p>
    </section>
</x-layouts::site>
