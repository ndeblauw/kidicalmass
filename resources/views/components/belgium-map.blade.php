@props([
    'map', // App\Support\Map\LocalGroupsMap::data(): outline, dots, Brussels bubble, label positions, accessible label
])

{{-- Static map of Belgium with every visible local group: a dot per group at its
     postcode centroid, all Brussels-Capital groups as one counted bubble. Pure
     server-rendered inline SVG (no JS, no tiles). Geometry and label placement
     are precomputed by App\Support\Map\BelgiumMap; this view only draws them.
     Region colours arrive as theme-token names from App\Enums\Region. Sizes are
     SVG user units from BelgiumMap, so the drawing scales with its box.

     The wrapper is a size container: place names show once the map itself is
     wide enough to read them (tablets too), and on a narrow map the markers grow
     instead (components/belgium-map.css). A page that shows the map twice (Home:
     sticky stage + inline copy) pays for the ~2 KB outline twice; gzip absorbs
     most of it, and a shared <symbol> would break when the defining copy is
     display:none. --}}
<div {{ $attributes->merge(['class' => 'belgium-map']) }} data-belgium-map>
    <svg class="belgium-map__svg"
         viewBox="0 0 {{ $map['width'] }} {{ $map['height'] }}"
         role="img"
         aria-label="{{ $map['label'] }}"
         xmlns="http://www.w3.org/2000/svg">
        <path class="belgium-map__land" d="{{ $map['outline'] }}" />

        @foreach ($map['dots'] as $dot)
            <circle class="belgium-map__dot" data-marker data-region="{{ $dot['region'] ?? 'other' }}"
                    cx="{{ $dot['x'] }}" cy="{{ $dot['y'] }}" r="{{ $dot['r'] }}"
                    style="color: var({{ $dot['colorToken'] }})" fill="currentColor" />
        @endforeach

        @if ($bubble = $map['bubble'])
            <g class="belgium-map__bubble" data-region="brussels" data-count="{{ $bubble['count'] }}"
               style="color: var({{ $bubble['colorToken'] }})">
                <circle cx="{{ $bubble['x'] }}" cy="{{ $bubble['y'] }}" r="{{ $bubble['r'] }}" fill="currentColor" />
                <text class="belgium-map__count" x="{{ $bubble['x'] }}" y="{{ $bubble['y'] }}"
                      text-anchor="middle" dominant-baseline="central" font-size="{{ $map['fontSize'] }}"
                      aria-hidden="true">{{ $bubble['count'] }}</text>
            </g>
        @endif

        <g class="belgium-map__labels" font-size="{{ $map['fontSize'] }}" aria-hidden="true">
            @foreach ([...($map['bubble'] ? [$map['bubble']] : []), ...$map['dots']] as $marker)
                @if ($marker['label'])
                    <text class="belgium-map__label" x="{{ $marker['label']['x'] }}" y="{{ $marker['label']['y'] }}"
                          text-anchor="{{ $marker['label']['anchor'] }}">{{ $marker['name'] }}</text>
                @endif
            @endforeach
        </g>
    </svg>
</div>
