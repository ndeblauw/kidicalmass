@props(['activity', 'cta' => false, 'heading' => 'h3', 'showTime' => true, 'showLocation' => true])

@php($image = $activity->getFirstMedia('main'))
<article {{ $attributes->merge(['class' => 'ride-spotlight']) }}>
    <div class="ride-spotlight__media{{ $image ? '' : ' ride-spotlight__media--empty' }}">
        @if ($image)
            <img src="{{ $image->getUrl('card') }}" alt="{{ $activity->title }}" class="ride-spotlight__img" loading="lazy">
        @else
            <span class="ride-spotlight__daisy" aria-hidden="true"></span>
        @endif
    </div>

    <div class="ride-spotlight__body">
        @if ($activity->groups->isNotEmpty())
            <p class="ride-spotlight__chapter">
                @foreach ($activity->groups as $group){{ $group->name }}@unless ($loop->last) · @endunless @endforeach
            </p>
        @endif

        <{{ $heading }} class="ride-spotlight__title">{{ $activity->title }}</{{ $heading }}>

        <p class="ride-spotlight__when">
            <time datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ \Illuminate\Support\Str::ucfirst($activity->dateFull) }}@if ($showTime) · {{ $activity->timeLabel }}@endif</time>
        </p>

        @if ($showLocation && $activity->location)
            <p class="ride-spotlight__loc">
                <flux:icon.map-pin variant="solid" class="ride-spotlight__loc-icon" aria-hidden="true" />
                Verzamelen: {{ $activity->location }}
            </p>
        @endif

        @if ($cta)
            <a href="{{ route('activities.show', $activity) }}" class="ride-spotlight__cta link-plain">Naar de rit →</a>
        @endif
    </div>
</article>
