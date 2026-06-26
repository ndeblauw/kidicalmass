@props(['activity', 'group', 'draft' => false])

{{-- One agenda row for the roze-hesjes hub, shared by both lists: a confirmed ride
     and a draft "in voorbereiding" use the very same lockup — date rail, title +
     meta, trailing chip. The draft state only softens it (dashed frame, muted rail),
     swaps the chip for an honest "Nog niet vast", and links to the live preview. --}}
@php
    $rail = \App\Support\RideDate::rail($activity->begin_date);
    $href = $draft
        ? route('groups.ride-preview', [$group, 'ride' => $activity->id])
        : route('activities.show', $activity);
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->class(['roze-agenda-row link-plain', 'roze-agenda-row--draft' => $draft]) }}
    @unless ($draft) style="--ride-accent: {{ $activity->activity_type->accentColor() }};" @endunless
>
    <time class="roze-agenda-row__date" datetime="{{ $activity->begin_date->toDateString() }}">
        <span class="roze-agenda-row__num">{{ $rail['num'] }}</span>
        <span class="roze-agenda-row__mon">{{ $rail['month'] }}</span>
    </time>

    <div class="roze-agenda-row__body">
        <strong class="roze-agenda-row__title roze-row-title">{{ $activity->title }}</strong>
        @unless ($draft)
            <span class="roze-agenda-row__meta">{{ ucfirst($activity->weekday_label) }} &middot; {{ $activity->time_label }}@if (filled($activity->location)) &middot; {{ $activity->location }}@endif</span>
        @endunless
    </div>

    <span class="roze-agenda-row__type">{{ $draft ? 'Nog niet vast' : $activity->activity_type->labelNl() }}</span>
</a>
