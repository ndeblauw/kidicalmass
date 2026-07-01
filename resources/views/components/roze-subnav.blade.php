@props([
    'tabs',              // array from HubTabs::for(...)
    'group',             // App\Models\Group — for route() binding
    'beheerUrl' => null, // external Filament URL for the Beheer tab
])

<nav class="roze-subnav" aria-label="Roze-hesje hub">
    <ul class="roze-subnav__list" role="list">
        @foreach ($tabs as $tab)
            <li>
                @if ($tab['external'])
                    <a href="{{ $beheerUrl }}" class="roze-subnav__beheer">
                        {{-- wrench --}}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.5 2.5-2.4-2.4 2.5-2.5z"></path></svg>
                        <span>{{ $tab['label'] }}</span>
                        {{-- external arrow --}}
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg>
                    </a>
                @else
                    <a
                        href="{{ route($tab['route'], $group) }}"
                        @class([
                            'roze-subnav__tab',
                            'roze-subnav__tab--active' => $tab['active'],
                        ])
                        @if ($tab['active']) aria-current="page" @endif
                    >{{ $tab['label'] }}</a>
                @endif
            </li>
        @endforeach
    </ul>
</nav>
