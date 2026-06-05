@php
    // National partners only (PAT-5: national vs local are different data).
    // Chapter-local partners (group_id set) belong on their chapter page, not on
    // this site-wide strip. Institutional-only curation awaits a category field (D-11).
    $partners = \App\Models\Partner::query()
        ->whereNull('group_id')
        ->where('visible', true)
        ->where('show_logo', true)
        ->get()
        ->reject(fn ($p) => \Illuminate\Support\Str::slug($p->name) === 'brussel-mobiliteit');

    $locale = app()->getLocale();
    $bmLogo = $locale === 'fr'
        ? asset('img/sponsors/bm-fr.avif')
        : asset('img/sponsors/bm-nl.avif');
    $bmAlt = $locale === 'fr'
        ? 'Avec le soutien de Bruxelles Mobilité'
        : 'Met de steun van Brussel Mobiliteit';
@endphp

{{-- Slim recognition strip (PAT-5). Site-wide, quiet: funder/partner logos +
     one link to the full Partners page. Acquisition ("partner worden"), the
     "Ook ondersteund door" list, and partner categories live on /about/partners. --}}
<aside class="partner-strip" aria-label="{{ __('partners.strip_label') }}">
    <div class="container mx-auto px-4">
        <div class="partner-strip__inner">
            <span class="partner-strip__label">{{ __('partners.supported_by') }}</span>

            <div class="partner-strip__logos">
                <img src="{{ $bmLogo }}" alt="{{ $bmAlt }}" class="partner-strip__logo partner-strip__logo--bm">
                @foreach($partners as $partner)
                    @php $logoUrl = $partner->getFirstMediaUrl('logo', 'partner'); @endphp
                    @if($logoUrl)
                        @if($partner->url)
                            <a href="{{ $partner->url }}" target="_blank" rel="noopener noreferrer" title="{{ $partner->name }}" class="partner-strip__logo-link">
                                <img src="{{ $logoUrl }}" alt="{{ $partner->name }}" class="partner-strip__logo">
                            </a>
                        @else
                            <img src="{{ $logoUrl }}" alt="{{ $partner->name }}" class="partner-strip__logo">
                        @endif
                    @else
                        @if($partner->url)
                            <a href="{{ $partner->url }}" target="_blank" rel="noopener noreferrer" class="partner-strip__chip">{{ $partner->name }}</a>
                        @else
                            <span class="partner-strip__chip">{{ $partner->name }}</span>
                        @endif
                    @endif
                @endforeach
            </div>

            <a href="{{ route('about.partners') }}" class="partner-strip__more">{{ __('partners.see_all') }} →</a>
        </div>
    </div>
</aside>
