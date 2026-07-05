@php
    // Partner showcase (PAT-5): the blue band of national partner logos renders only
    // on the home page and the About narrative pages. The funder acknowledgment
    // (Brussel Mobiliteit) now lives quietly in the footer, site-wide.
    $showcaseRoutes = ['home', 'about', 'about.mission', 'about.vision', 'about.organisation'];
    if (! in_array(request()->route()?->getName(), $showcaseRoutes, true)) {
        return;
    }

    // National partners only (PAT-5: national vs local are different data).
    // Chapter-local partners (group_id set) belong on their chapter page, not on
    // this site-wide strip. Institutional-only curation awaits a category field (D-11).
    // Brussel Mobiliteit is excluded here because it lives in the footer funder line.
    $partners = \App\Models\Partner::query()
        ->whereNull('group_id')
        ->where('visible', true)
        ->where('show_logo', true)
        ->with('media')
        ->get()
        ->reject(fn ($p) => \Illuminate\Support\Str::slug($p->name) === 'brussel-mobiliteit');
@endphp

{{-- Partner showcase (PAT-5). Home + About narrative pages only. The blue band of
     national partner logos + one link to the full Partners page. Acquisition
     ("partner worden"), the "Ook ondersteund door" list, and partner categories
     live on /about/partners. --}}
<aside class="partner-strip container mx-auto px-4" aria-label="{{ __('partners.strip_label') }}">
    <div class="partner-strip__inner">
        <span class="partner-strip__label">{{ __('partners.showcase_label') }}</span>

        <ul class="partner-strip__logos" role="list">
            @foreach($partners as $partner)
                @php $logoUrl = $partner->getFirstMediaUrl('logo', 'partner'); @endphp
                <li>
                    @if($logoUrl)
                        @if($partner->url)
                            <a href="{{ $partner->url }}" target="_blank" rel="noopener noreferrer" title="{{ $partner->name }}" class="partner-strip__logo-link">
                                <img src="{{ $logoUrl }}" alt="{{ $partner->name }}" class="partner-strip__logo" loading="lazy" decoding="async">
                            </a>
                        @else
                            <img src="{{ $logoUrl }}" alt="{{ $partner->name }}" class="partner-strip__logo" loading="lazy" decoding="async">
                        @endif
                    @else
                        @if($partner->url)
                            <a href="{{ $partner->url }}" target="_blank" rel="noopener noreferrer" class="partner-strip__chip">{{ $partner->name }}</a>
                        @else
                            <span class="partner-strip__chip">{{ $partner->name }}</span>
                        @endif
                    @endif
                </li>
            @endforeach
        </ul>

        <a href="{{ route('about.partners') }}" class="partner-strip__more">{{ __('partners.see_all') }} →</a>
    </div>
</aside>
