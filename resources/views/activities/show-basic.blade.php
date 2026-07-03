{{--
    BASIC ACTIVITY PAGE — the detail page for a non-ride activity (workshop / meeting /
    other). Chosen over the ride layout (activities/show.blade.php) by ActivityController
    when the type isn't a ride: a workshop has no route map, no pace promises and no
    pink-vest-on-the-ride ask, so that whole ride spine would render empty or misleading.

    Shape: blue poster HERO (reused look) -> single-column body where the DESCRIPTION
    leads, over a compact meta row (no map) -> an optional "Meer info" CTA to the external
    event (commute_link, e.g. the Facebook event the live site links to) -> a light
    organizer line -> share (skipped for volunteer meetings) -> closing back to the chapter.

    Register shifts by type (D-2 / chapters v3): workshop = public + warm; meeting = for
    volunteers, no family CTA, no broadcast share. NL, on the public site kit. Structure
    here; appearance in resources/css/pages/activity.css (.activity-basic*).
--}}
<x-layouts::site title="{{ $activity->title_nl }}" :description="$activity->metaDescription()" :og-image="$activity->ogImageUrl()">
    @php
        $type = $activity->activity_type;
        $isMeeting = $type === \App\Enums\ActivityType::MEETING;
        $mainImage = $activity->getFirstMedia('main');
        $chapter = $activity->groups->first();
        $gemeente = $chapter
            ? trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $chapter->name))
            : null;
    @endphp

    {{-- HERO — the shared blue poster look (.activity-hero), with a type eyebrow added
         above the title so a workshop/meeting announces what it is up front. --}}
    <section class="activity-hero">
        <img src="{{ asset('img/logos/logo-icon.png') }}" alt="" aria-hidden="true" class="activity-hero__daisy">

        <div class="container mx-auto px-4 activity-hero__inner">
            <div class="activity-hero__copy">
                <p class="activity-basic__type">{{ $type->labelNl() }}</p>
                <h1>{{ $activity->title_nl }}</h1>

                <p class="activity-hero__date">
                    <time datetime="{{ $activity->begin_date->toIso8601String() }}">{{ \Illuminate\Support\Str::ucfirst($activity->begin_date->translatedFormat('l j F')) }}</time>
                </p>

                @if ($chapter)
                    <div class="activity-hero__chapter">
                        <svg class="activity-hero__chapter-pin" viewBox="0 0 40 54" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M20 2C10.059 2 2 10.059 2 20C2 32 20 52 20 52C20 52 38 32 38 20C38 10.059 29.941 2 20 2Z" fill="var(--color-kidical-red)"/>
                            <circle cx="20" cy="20" r="7.5" fill="rgba(0,0,0,0.25)"/>
                            <circle cx="20" cy="20" r="4.5" fill="white"/>
                        </svg>
                        <div class="activity-hero__chapter-label">
                            <span>{{ $chapter->name }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="activity-hero__visual">
                @if ($mainImage)
                    <div class="activity-hero__photo">
                        <img src="{{ $mainImage->getUrl() }}" @if ($mainImage->getSrcset()) srcset="{{ $mainImage->getSrcset() }}" sizes="(min-width: 768px) 50vw, 100vw" @endif alt="{{ $activity->title_nl }}" class="activity-hero__img" fetchpriority="high">
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- BODY — single column, description-led. A compact meta row sits above the prose
         (no full-bleed map panel; a non-ride has nowhere to ride). The external "Meer
         info" link closes the block when the activity has one. --}}
    <section class="activity-basic container mx-auto px-4">
        <div class="activity-basic__inner">
            <dl class="activity-basic__meta">
                <div class="activity-basic__meta-item">
                    <flux:icon.clock variant="solid" class="activity-basic__meta-icon" aria-hidden="true" />
                    <dt class="sr-only">Startuur</dt>
                    <dd><time datetime="{{ $activity->begin_date->toIso8601String() }}">{{ $activity->timeLabel }}</time></dd>
                </div>

                @if ($activity->location)
                    <div class="activity-basic__meta-item">
                        <flux:icon.map-pin variant="solid" class="activity-basic__meta-icon" aria-hidden="true" />
                        <dt class="sr-only">Waar</dt>
                        <dd>{!! nl2br(e($activity->location)) !!}</dd>
                    </div>
                @endif

                @if ($activity->duration_label)
                    <div class="activity-basic__meta-item">
                        <flux:icon.arrow-path variant="solid" class="activity-basic__meta-icon" aria-hidden="true" />
                        <dt class="sr-only">Duur</dt>
                        <dd>{{ $activity->duration_label }}</dd>
                    </div>
                @endif
            </dl>

            @if ($activity->content_nl)
                <div class="activity-basic__prose">
                    {!! nl2br(e($activity->content_nl)) !!}
                </div>
            @endif

            @if ($activity->commute_link)
                <div class="activity-basic__cta">
                    <x-cta-button
                        variant="{{ $isMeeting ? 'secondary' : 'blue' }}"
                        href="{{ $activity->commute_link }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >{{ $isMeeting ? 'Meer info voor vrijwilligers' : 'Meer info' }}</x-cta-button>
                </div>
            @endif

            {{-- Light organizer line — who runs it, linking back to the chapter. No
                 pink-vest-on-the-ride form here (that's a ride thing). --}}
            @if ($chapter)
                <p class="activity-basic__organizer">
                    Georganiseerd door vrijwilligers van
                    <a href="{{ route('groups.show', $chapter) }}">{{ $chapter->name }}</a>.
                </p>
            @endif
        </div>
    </section>

    {{-- SHARE — warm "invite someone" moment for public activities. Skipped for
         volunteer meetings (internal; nothing to broadcast). Custom copy so it doesn't
         inherit the ride share-band's "een vrolijke gezinsrit" wording. --}}
    @unless ($isMeeting)
        <x-share-band
            :url="route('activities.show', $activity)"
            :title="$activity->title_nl"
            :date="$activity->begin_date->translatedFormat('l j F')"
            heading="Ken je iemand die hierbij wil zijn?"
            subline="Stuur het door, samen is altijd leuker."
            :message="'Kom je naar '.$activity->title_nl.' op '.$activity->begin_date->translatedFormat('l j F').'? '.route('activities.show', $activity)"
            subject="Een leuke activiteit van Kidical Mass" />
    @endunless

    @if ($chapter)
        <x-slot:closing>
            <x-closing-cta
                heading="Meer uit Kidical Mass {{ $gemeente }}?"
                :href="route('groups.show', $chapter)"
                label="Naar de buurtpagina" />
        </x-slot:closing>
    @endif
</x-layouts::site>
