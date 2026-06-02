@php
    $partners = \App\Models\Partner::where('visible', true)
        ->where('show_logo', true)
        ->get();

    $locale = app()->getLocale();
    $bmLogo = $locale === 'fr'
        ? asset('img/sponsors/bm-fr.avif')
        : asset('img/sponsors/bm-nl.avif');
    $bmAlt = $locale === 'fr'
        ? 'Avec le soutien de Bruxelles Mobilité'
        : 'Met de steun van Brussel Mobiliteit';
@endphp

<section class="partners-section">
    <div class="partners-inner container mx-auto px-4">

        {{-- Left column: all content --}}
        <div class="partners-left">

            <div class="partners-heading-content">
                <h2>{{ __('partners.heading') }}</h2>
                <p class="partners-cta">{{ __('partners.cta') }} <a href="#">{{ __('partners.sponsor_formulas') }}</a> · <a href="#">{{ __('partners.partner_charter') }}</a></p>
            </div>

            @if($partners->isNotEmpty())
                <div class="partners-logo-strip">
                    @foreach($partners as $partner)
                        @php $logoUrl = $partner->getFirstMediaUrl('logo', 'partner'); @endphp
                        @if($logoUrl)
                            @if($partner->url)
                                <a href="{{ $partner->url }}" target="_blank" rel="noopener noreferrer" title="{{ $partner->name }}" class="partners-logo-link">
                                    <img src="{{ $logoUrl }}" alt="{{ $partner->name }}" class="partners-logo">
                                </a>
                            @else
                                <img src="{{ $logoUrl }}" alt="{{ $partner->name }}" class="partners-logo">
                            @endif
                        @endif
                    @endforeach
                </div>
            @endif

            <div class="partners-bottom-band">
                <div class="partners-bm-col">
                    <img src="{{ $bmLogo }}" alt="{{ $bmAlt }}" class="partners-bm-logo">
                </div>
                <div class="partners-links-col">
                    <span class="partners-also-label">{{ __('partners.also_supported_by') }}</span>
                    <ul class="partners-links-list">
                        <li><a href="#">Clean Cities</a></li>
                        <li><a href="#">Bruxelles Ville / Brussel Stad</a></li>
                        <li><a href="#">La commune de Schaerbeek / Gemeente Schaerbeek</a></li>
                        <li><a href="#">Our spacefunders</a></li>
                    </ul>
                </div>
            </div>

        </div>

        {{-- Right column: illustration spanning full section height --}}
        <div class="partners-illustration-col" aria-hidden="true">
            <img src="{{ asset('img/illustrations/kid-on-bike-teal.png') }}" alt="">
        </div>

    </div>
</section>
