@props(['title' => 'Want to be our Partner/Sponsor? (local/regional)'])

@php
    $partners = \App\Models\Partner::where('visible', true)
        ->where('show_logo', true)
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
        <div class="grid grid-cols-8 gap-6 md:gap-8">
            @foreach($partners as $partner)
                @php
                    $logo = $partner->getFirstMediaUrl('logo');
                @endphp

                @if($logo)
                    <div class="flex-shrink-0">
                        @if($partner->url)
                            <a href="{{ $partner->url }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               title="{{ $partner->name }}"
                               class=" hover:opacity-80 transition-opacity">
                                <img src="{{ $logo }}"
                                     alt="{{ $partner->name }}"
                                     class="h-20 w-auto rounded-lg object-contain grayscale hover:grayscale-0 transition-all duration-300">
                            </a>
                        @else
                            <div class="rounded-lg bg-white flex justify-center items-center">
                                <img src="{{ $logo->getUrl('partner') }}"
                                     alt="{{ $partner->name }}"
                                     class="h-20 w-auto object-contain grayscale hover:grayscale-0 transition-all duration-300">
                            </div>
                        @endif
                    </div>
                @else
                    <div class="rounded-lg bg-white flex justify-center items-center"> {{ $partner->name }}</div>
                @endif
            @endforeach
        </div>

        <!-- Financial Support Text -->
        <div class="text-center mt-12 text-gray-700">
            <p class="text-lg">
                Avec le soutien financier de / Dankzij steun van :
            </p>
            <p class="mt-2 text-sm">
                Bruxelles Mobilité/ Brussel Mobiliteit, Clean Cities,<br>
                Bruxelles Ville/Brussel Stad, La commune de Schaerbeek<br>
                / gemeente Schaerbeek en onze/et nos
                <span class="text-kidical-blue font-semibold">spacefunders</span>.
            </p>
        </div>
    </div>
</div>
@endif
