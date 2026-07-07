{{--
    BASIC ACTIVITY PAGE — the detail page for a non-ride activity (workshop / meeting /
    other). Chosen over the ride layout (activities/show.blade.php) by ActivityController
    when the type isn't a ride: a workshop has no route, no pace promises and no
    pink-vest-on-the-ride ask.

    Shape (review 07-07): the SAME hero grammar as a ride — date tear-off beside the
    title, intro beneath, tilted photo right, zip chip in the nav — minus the parade
    illustrations. The white body mirrors the ride's Praktisch section: facts card +
    map, but with a location pin instead of a route (no Komoot, no afstand/deelname).
    The pin centres on the activity's postal code (PostalCode::coordinatesFor); when
    no coordinate is known the card carries a "Waar" row instead of a map.

    Register shifts by type (D-2 / chapters v3): workshop = public + warm; meeting = for
    volunteers, no family CTA, no share ask. NL, on the public site kit. Structure
    here; appearance in resources/css/pages/activity.css.
--}}
<x-layouts::site title="{{ $activity->title_nl }}" :nav-chapter="$activity->groups->first()" :description="$activity->metaDescription()" :og-image="$activity->ogImageUrl()">
    @php
        $type = $activity->activity_type;
        $isMeeting = $type === \App\Enums\ActivityType::MEETING;
        $isPast = $activity->hasEnded();
        $mainImage = $activity->getFirstMedia('main');
        $chapter = $activity->groups->first();
        $gemeente = $chapter
            ? trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $chapter->name))
            : null;

        $venue = \Illuminate\Support\Str::of($activity->location)->replace("\n", ', ')->trim();
        $pinCoords = \App\Models\PostalCode::coordinatesFor($activity->postal_code ?: (string) $chapter?->zip);
        $pin = $pinCoords ? [[$pinCoords['lat'], $pinCoords['lng']]] : [];
    @endphp

    {{-- HERO — the ride hero's blue poster (date tear-off + title, intro beneath,
         tilted photo right), without the parade riders: a workshop or meeting reuses
         the family look but doesn't announce a parade. --}}
    <header class="activity-head activity-head--basic">
        <div class="container mx-auto px-4 activity-head__inner">

            <div class="activity-head__copy">
                @if($isPast)
                    <p class="activity-head__past">Voorbij</p>
                @endif

                <p class="activity-basic__type">{{ $type->labelNl() }}</p>

                <div class="activity-head__headline">
                    <x-ride-date-tile
                        :date="$activity->begin_date"
                        accent="var(--color-kidical-red)"
                        :rotation="-3"
                        size="lg"
                        class="activity-head__date" />
                    <h1 class="page-hero__title">{{ $activity->title_nl }}</h1>
                </div>

                @if($activity->content_nl)
                    <x-intro-text class="activity-head__lead">{!! nl2br(e($activity->content_nl)) !!}</x-intro-text>
                @endif
            </div>

            @if($mainImage)
                <figure class="activity-head__media">
                    <img src="{{ $mainImage->getUrl() }}" @if ($mainImage->getSrcset()) srcset="{{ $mainImage->getSrcset() }}" sizes="100vw" @endif alt="{{ $activity->title_nl }}" class="activity-head__photo" fetchpriority="high">
                </figure>
            @endif

        </div>
    </header>

    {{-- BODY PANEL — the ride page's white sheet, basic flavour. --}}
    <div class="activity-stack" data-activity-layout="basic">
        <div class="activity-stack__inner container mx-auto px-4">

        {{-- PRAKTISCH — the ride's facts-card grammar with non-ride content: when +
             duration, and the venue pinned on a map (no route to draw). --}}
        <section class="activity-praktisch">
            <article class="activity-facts">
                <div class="activity-facts__body">
                    <div class="activity-facts__main">
                        <dl class="activity-facts__meta">
                            <div class="activity-facts__meta-item">
                                <x-icon-chip color="red" size="sm" aria-hidden="true">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4.5" width="18" height="16.5" rx="2"/><path d="M3 9.5h18M8 2.5v4M16 2.5v4"/></svg>
                                </x-icon-chip>
                                <div>
                                    <dt>Wanneer</dt>
                                    <dd><time datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ \Illuminate\Support\Str::ucfirst($activity->dateFull) }}, {{ $activity->timeLabel }}</time></dd>
                                </div>
                            </div>

                            @if($activity->duration_label)
                                <div class="activity-facts__meta-item">
                                    <x-icon-chip color="red" size="sm" aria-hidden="true">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                    </x-icon-chip>
                                    <div>
                                        <dt>Duur</dt>
                                        <dd>{{ $activity->duration_label }}</dd>
                                    </div>
                                </div>
                            @endif

                            @if($venue->isNotEmpty() && empty($pin))
                                <div class="activity-facts__meta-item">
                                    <x-icon-chip color="red" size="sm" aria-hidden="true">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-5.5-7-11a7 7 0 0 1 14 0c0 5.5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                                    </x-icon-chip>
                                    <div>
                                        <dt>Waar</dt>
                                        <dd>{{ $venue }}</dd>
                                    </div>
                                </div>
                            @endif
                        </dl>
                    </div>

                    @if(!empty($pin))
                        {{-- Location map — the ride map's look with a single pin on the
                             venue's postal-code centre, no route line. The <dl> below is
                             the accessible, no-JS fallback for the pin popup. --}}
                        <div class="activity-facts__map">
                            <x-route-map :coordinates="$pin" :interactive="false" label="{{ $venue }}" eyebrow="Locatie" class="activity-facts__route" aria-hidden="true" />
                            <dl class="activity-facts__map-label activity-facts__map-label--fallback">
                                <dt>Locatie</dt>
                                <dd>{{ $venue }}</dd>
                            </dl>

                            @if($activity->commute_link)
                                <x-cta-button
                                    :href="$activity->commute_link"
                                    variant="secondary"
                                    size="sm"
                                    icon="arrow"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="activity-facts__map-cta"
                                >{{ $isMeeting ? 'Meer info voor vrijwilligers' : 'Meer info' }}</x-cta-button>
                            @endif
                        </div>
                    @endif
                </div>
            </article>

            {{-- DEEL — the ride's quiet share ask. Skipped for volunteer meetings
                 (internal; nothing to broadcast). --}}
            @unless($isMeeting)
                <aside class="activity-share">
                    <div class="activity-share__text">
                        @if($isPast)
                            <h2>Deel de herinnering</h2>
                            <p class="activity-share__body">Laat anderen zien hoe fijn het was.</p>
                        @else
                            <h2>Vrienden mee?</h2>
                            <p class="activity-share__body">Stuur het door, samen is altijd leuker.</p>
                        @endif
                    </div>

                    <x-share-links
                        :url="route('activities.show', $activity)"
                        :title="$activity->title_nl"
                        :date="$activity->begin_date->translatedFormat('l j F')" />
                </aside>
            @endunless
        </section>

        {{-- Without a map there is no corner slot, so the external link gets its own
             quiet CTA block. --}}
        @if($activity->commute_link && empty($pin))
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
    </div>

    @if ($chapter)
        <x-slot:closing>
            <x-closing-cta
                heading="Meer uit Kidical Mass {{ $gemeente }}?"
                :href="route('groups.show', $chapter)"
                label="Naar de buurtpagina" />
        </x-slot:closing>
    @endif
</x-layouts::site>
