<x-layouts::site :title="__('roze.preview.page_title', ['name' => $group->name])">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;

        $title = $ride?->title ?? __('roze.preview.untitled', ['place' => $gemeente]);
        $when = $ride
            ? __('roze.preview.when', ['date' => $ride->date_full])
            : __('roze.preview.when_faux');
        $whenIso = $ride?->begin_date?->toDateString() ?? '2026-07-12';
    @endphp

    {{-- Read-only preview of a draft ride. Shows a real unpublished Activity when one is
         passed (?ride=), else a faux exemplar. The status line stays prose — there is no
         Activity status field yet (Nico #37) — and is read-only for hesjes. --}}
    <section class="chapter-body roze-preview">
        <a href="{{ localized_route('groups.roze-hesjes.agenda', ['group' => $group]) }}" class="roze-preview__back link-plain">&larr; {{ __('roze.preview.back') }}</a>

        <p class="roze-preview__flag">{{ __('roze.preview.flag') }}</p>
        <h1>{{ $title }}</h1>
        <p class="roze-preview__when"><time datetime="{{ $whenIso }}">{{ $when }}</time></p>

        <div class="roze-preview__status">
            <strong class="roze-preview__status-title roze-card-title">{{ __('roze.preview.status_title') }}</strong>
            <p class="roze-preview__status-body">{{ __('roze.preview.status_body') }}</p>
        </div>

        <p class="roze-preview__foot">{{ __('roze.preview.foot') }}</p>
    </section>
</x-layouts::site>
