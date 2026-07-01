<x-roze-hub :group="$group" active="overzicht" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl">
    @php
        $nextRail = $nextRide ? \App\Support\RideDate::rail($nextRide->begin_date) : null;
    @endphp

    <div class="roze-overview">
        @if ($nextRide)
            <section class="roze-next-section">
                <h2 class="roze-hub-subtitle">Je volgende rit</h2>
                <a href="{{ route('activities.show', $nextRide) }}" class="roze-next" style="--ride-accent: {{ $nextRide->activity_type->accentColor() }};">
                    <time class="roze-next__date" datetime="{{ $nextRide->begin_date->toDateString() }}">
                        <span class="roze-next__num">{{ $nextRail['num'] }}</span>
                        <span class="roze-next__mon">{{ $nextRail['month'] }}</span>
                    </time>
                    <span class="roze-next__body">
                        <strong class="roze-next__title roze-row-title">{{ $nextRide->title }}</strong>
                        <span class="roze-next__meta">{{ ucfirst($nextRide->weekday_label) }} &middot; {{ $nextRide->time_label }}@if (filled($nextRide->location)) &middot; {{ $nextRide->location }}@endif</span>
                    </span>
                    <svg class="roze-next__chev" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            </section>
        @endif

        <section class="roze-grab">
            <h2 class="roze-hub-subtitle">Voor de rit</h2>
            <div class="roze-grab__tiles">
                <a href="{{ route('groups.roze-hesjes.materiaal', $group) }}" class="roze-tile">
                    <x-icon-chip color="orange" size="sm" :shadow="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                    </x-icon-chip>
                    <span class="roze-tile__label roze-row-title">Speech</span>
                </a>
                {{-- Per-chapter playlist URL is faux for now (Nico #37). --}}
                <a href="{{ route('groups.roze-hesjes.materiaal', $group) }}" class="roze-tile">
                    <x-icon-chip color="violet" size="sm" :shadow="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    </x-icon-chip>
                    <span class="roze-tile__label roze-row-title">Playlist</span>
                </a>
            </div>
        </section>

        @if (count($feed) > 0)
        <section class="roze-feeds">
            <h2 class="roze-hub-subtitle">Sinds je laatste bezoek</h2>
            <p class="roze-hub-lead">Wat er veranderde terwijl je weg was.</p>
            <div class="roze-feeds__list">
                @foreach ($feed as $item)
                    <x-roze-feed-card
                        :color="$item['color']"
                        :icon="$item['icon']"
                        :what="$item['what']"
                        :context="$item['context']"
                        :timestamp="$item['timestamp']"
                        :relative="$item['relative']"
                        :href="$item['href']"
                    />
                @endforeach
            </div>
        </section>
        @endif
    </div>
</x-roze-hub>
