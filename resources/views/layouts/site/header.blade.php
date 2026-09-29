@props(['chapter' => null])

@php
    // The visitor's chapters drive the roze nav button(s); compute once, reuse in both navs.
    $myChapters = Auth::check()
        ? Auth::user()->groups()->where('invisible', false)->orderBy('name_nl')->get()
        : collect();

    $isHome = route_is('home');

    // Show a group's postcode beside the logo. On a chapter page the route binds a {group};
    // pages without that binding (e.g. a ride) can pass the organising group via :chapter.
    $navChapter = $chapter ?? request()->route('group');
    $navChapter = $navChapter instanceof \App\Models\Group ? $navChapter : null;

    // Members carry extra nav buttons (their chapters + account), which only fit
    // beside the logo from xl; visitors get the full nav from lg.
    $fullNavClasses = Auth::check() ? ['show' => 'hidden xl:flex', 'hide' => 'xl:hidden'] : ['show' => 'hidden lg:flex', 'hide' => 'lg:hidden'];
@endphp

{{-- Logo: fixed at hero z-level so page panels scroll over it --}}
<div class="site-logo-anchor @if ($isHome) site-logo-anchor--intro @endif">
    <div class="container mx-auto px-4">
        <a href="{{ route('home') }}" class="site-logo-anchor__link">
            <img
                src="{{ asset('img/logos/footer-logo.avif') }}"
                alt="Kidical Mass"
                class="site-nav__logo w-auto"
            >
            @if ($navChapter?->zip)
                <span class="site-nav__postcode" data-nav-postcode="{{ $navChapter->zip }}">{{ $navChapter->zip }}</span>
            @endif
        </a>
    </div>
</div>

<header class="site-header @if ($isHome) site-header--intro @endif" x-data="{ mobileOpen: false }" x-on:keydown.escape.window="mobileOpen = false">
    <div class="container mx-auto px-4">
        <div class="site-nav">
            <div class="site-nav__bar flex items-center justify-end gap-4">
                <!-- Desktop: nav links in their own white band + support CTA (+ member items) -->
                <div class="site-nav__group site-nav__reveal-menu {{ $fullNavClasses['show'] }}">
                    <div class="site-nav__links">
                        <flux:navbar aria-label="{{ __('nav.main_menu') }}">
                            <flux:navbar.item href="{{ localized_route('activities.index') }}" :current="route_is('activities.*')" class="font-bold text-lg">{{ __('nav.events') }}</flux:navbar.item>
                            <flux:navbar.item href="{{ localized_route('groups.index') }}" :current="route_is('groups.*')" class="font-bold text-lg">{{ __('nav.chapters') }}</flux:navbar.item>
                            <flux:navbar.item href="{{ localized_route('getting-started') }}" :current="route_is('getting-started')" class="font-bold text-lg">{{ __('nav.getting_started') }}</flux:navbar.item>
                            <flux:navbar.item href="{{ localized_route('volunteer') }}" :current="route_is('volunteer')" class="font-bold text-lg">{{ __('nav.help_out') }}</flux:navbar.item>
                            <flux:navbar.item href="{{ localized_route('about') }}" :current="route_is('about', 'about.*', 'articles.*')" class="font-bold text-lg">{{ __('nav.about') }}</flux:navbar.item>
                        </flux:navbar>
                    </div>

                    <a href="{{ localized_route('membership') }}" class="steun-nav-btn">
                        <flux:icon name="heart" variant="solid" class="size-4" aria-hidden="true" />
                        {{ __('support.nav') }}
                    </a>

                    @auth
                        @foreach ($myChapters as $myChapter)
                            <a href="{{ localized_route('groups.roze-hesjes', ['group' => $myChapter]) }}"
                               class="roze-nav-btn {{ route_is('groups.roze-hesjes', 'groups.roze-hesjes.*') && optional(request()->route('group'))->is($myChapter) ? 'roze-nav-btn--active' : '' }}">
                                {{ \Illuminate\Support\Str::of($myChapter->name)->replaceMatches('/^\s*kidical\s+mass\s+/i', '')->trim() }}
                            </a>
                        @endforeach
                        <x-account-menu />
                    @endauth

                    <x-language-switch variant="desktop" />
                </div>

                <!-- Mobile + tablet: everything lives behind the toggle -->
                <div class="site-nav__reveal-menu flex items-center {{ $fullNavClasses['hide'] }}">
                    <button type="button" x-on:click="mobileOpen = !mobileOpen"
                            aria-label="{{ __('nav.menu') }}"
                            aria-expanded="false" x-bind:aria-expanded="mobileOpen.toString()"
                            aria-controls="site-mobile-menu"
                            class="mobile-menu-toggle">
                        <flux:icon.bars-3 x-show="! mobileOpen" class="size-6" aria-hidden="true" />
                        <flux:icon.x-mark x-show="mobileOpen" x-cloak class="size-6" aria-hidden="true" />
                    </button>
                </div>
            </div>

            <!-- Mobile + tablet: full-screen menu. The bar above stays on top so the toggle closes it. -->
            <nav id="site-mobile-menu" aria-label="{{ __('nav.main_menu') }}"
                 x-show="mobileOpen" x-cloak x-transition.opacity.duration.150ms
                 x-effect="document.documentElement.classList.toggle('mobile-menu-open', mobileOpen)"
                 class="mobile-menu {{ $fullNavClasses['hide'] }}">
                <div class="mobile-menu__inner container mx-auto px-4">
                    <a href="{{ route('home') }}" class="mobile-menu__logo">
                        <img src="{{ asset('img/logos/footer-logo.avif') }}" alt="Kidical Mass" class="h-full w-auto">
                    </a>

                    @auth
                        <div class="mobile-menu__chapters">
                            @foreach ($myChapters as $myChapter)
                                <a href="{{ localized_route('groups.roze-hesjes', ['group' => $myChapter]) }}" class="roze-nav-btn roze-nav-btn--block">
                                    {{ \Illuminate\Support\Str::of($myChapter->name)->replaceMatches('/^\s*kidical\s+mass\s+/i', '')->trim() }}
                                </a>
                            @endforeach
                        </div>
                    @endauth

                    <ul class="mobile-menu__links">
                        @foreach ([
                            ['route' => 'activities.index', 'current' => route_is('activities.*'), 'label' => __('nav.events')],
                            ['route' => 'groups.index', 'current' => route_is('groups.*'), 'label' => __('nav.chapters')],
                            ['route' => 'getting-started', 'current' => route_is('getting-started'), 'label' => __('nav.getting_started')],
                            ['route' => 'volunteer', 'current' => route_is('volunteer'), 'label' => __('nav.help_out')],
                            ['route' => 'about', 'current' => route_is('about', 'about.*', 'articles.*'), 'label' => __('nav.about')],
                        ] as $link)
                            <li>
                                <a href="{{ localized_route($link['route']) }}"
                                   class="mobile-menu__link"
                                   @if ($link['current']) aria-current="page" @endif>{{ $link['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mobile-menu__footer">
                        @auth
                            <ul class="mobile-menu__account" aria-label="{{ __('nav.account') }}">
                                <li><a href="{{ route('settings') }}" class="mobile-menu__account-link" wire:navigate>{{ __('nav.settings') }}</a></li>
                                @if (Auth::user()->canAccessFilament())
                                    <li><a href="{{ url('/admin') }}" class="mobile-menu__account-link">{{ __('nav.admin') }}</a></li>
                                @endif
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="mobile-menu__account-link">{{ __('auth.logout') }}</button>
                                    </form>
                                </li>
                            </ul>
                        @endauth

                        <a href="{{ localized_route('membership') }}" class="steun-nav-btn steun-nav-btn--block">
                            <flux:icon name="heart" variant="solid" class="size-5" aria-hidden="true" />
                            {{ __('support.nav') }}
                        </a>
                        <x-language-switch variant="mobile" />
                    </div>
                </div>
            </nav>
        </div>
    </div>
</header>
