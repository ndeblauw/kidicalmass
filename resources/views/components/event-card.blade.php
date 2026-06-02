@props(['activity'])

{{-- PAT-1 · Event card (compact). Whole card links to the event; text-only per the events spec. --}}
<a
    href="{{ route('activities.show', $activity) }}"
    {{ $attributes->merge(['class' => 'link-plain group block h-full rounded-xl border border-kidical-ink/10 bg-white p-5 shadow-sm transition-shadow hover:shadow-md']) }}
>
    <div class="text-sm font-bold uppercase tracking-wide text-kidical-red">
        <time datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">
            {{ $activity->begin_date->format('D j M') }} · {{ $activity->begin_date->format('H:i') }}
        </time>
    </div>

    <h3 class="mt-1 text-lg text-kidical-blue group-hover:text-kidical-orange transition-colors">{{ $activity->title_nl }}</h3>

    @if ($activity->location)
        <p class="mt-1 text-sm">{{ $activity->location }}</p>
    @endif

    @if ($activity->groups->isNotEmpty())
        <p class="mt-3 text-xs font-semibold text-kidical-ink/50">{{ $activity->groups->pluck('name')->join(' · ') }}</p>
    @endif
</a>
