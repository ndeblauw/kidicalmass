@props(['title' => 'Want to be our Partner/Sponsor? (local/regional)'])

@php
    $partners = \App\Models\Partner::where('visible', true)
        ->where('show_logo', true)
        ->with('group')
        ->get();
@endphp

@if($partners->count() > 0)
<div class="bg-gray-50 py-12 mt-16">
    <div class="container mx-auto px-4">
        <!-- Title -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-kidical-blue mb-2">
                {{ $title }}
                <a href="mailto:info@kidicalmass.be" class="text-kidical-orange hover:underline">Mail</a> us!
            </h2>
        </div>

        <!-- Partner Logos Grid -->
        <div class="flex flex-wrap items-center justify-center gap-6 md:gap-8">
            @foreach($partners as $partner)
                @php
                    $logo = $partner->getFirstMedia('logo');
                @endphp
                
                @if($logo)
                    <div class="flex-shrink-0">
                        @if($partner->url)
                            <a href="{{ $partner->url }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               title="{{ $partner->name }}"
                               class="block hover:opacity-80 transition-opacity">
                                <img src="{{ $logo->getUrl('partner') }}" 
                                     alt="{{ $partner->name }}"
                                     class="h-20 w-auto object-contain grayscale hover:grayscale-0 transition-all duration-300">
                            </a>
                        @else
                            <div title="{{ $partner->name }}">
                                <img src="{{ $logo->getUrl('partner') }}" 
                                     alt="{{ $partner->name }}"
                                     class="h-20 w-auto object-contain grayscale hover:grayscale-0 transition-all duration-300">
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Financial Support Text -->
        @php
            $locale = app()->getLocale();
        @endphp
        <div class="text-center mt-12 text-gray-700">
            @if($locale === 'fr')
                <p class="text-lg">
                    Avec le soutien financier de / Dankzij steun van :
                </p>
            @else
                <p class="text-lg">
                    Avec le soutien financier de / Dankzij steun van :
                </p>
            @endif
            <p class="mt-2 text-sm">
                Bruxelles Mobilité/ Brussel Mobiliteit, Clean Cities,<br>
                Bruxelles Ville/Brussel Stad, La commune de Schaerbeek<br>
                / gemeente Schaerbeek en onze/et nos 
                <a href="#" class="text-kidical-blue hover:underline font-semibold">spacefunders</a>.
            </p>
        </div>
    </div>
</div>
@endif
