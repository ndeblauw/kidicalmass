@props(['chapter' => null])

@php
    // The visitor's chapters drive the roze nav button(s); compute once, reuse in both navs.
    $myChapters = Auth::check()
        ? Auth::user()->groups()->where('invisible', false)->orderBy('name_nl')->get()
        : collect();

    $isHome = request()->routeIs('home', 'fr.home');

    // Show a group's postcode beside the logo. On a chapter page the route binds a {group};
    // pages without that binding (e.g. a ride) can pass the organising group via :chapter.
    $navChapter = $chapter ?? request()->route('group');
    $navChapter = $navChapter instanceof \App\Models\Group ? $navChapter : null;
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

<header class="site-header @if ($isHome) site-header--intro @endif" x-data="{ mobileOpen: false }">
    <div class="container mx-auto px-4">
        <div class="site-nav">
            <div class="site-nav__bar flex items-center justify-end gap-4">
                <!-- Desktop: nav links in their own white band + support CTA (+ member items) -->
                <div class="site-nav__group site-nav__reveal-menu hidden md:flex">
                    <flux:navbar class="site-nav__links" aria-label="{{ __('nav.main_menu') }}">
                        <flux:navbar.item href="{{ localized_route('activities.index') }}" :current="request()->routeIs('activities.*')" class="font-bold text-lg">{{ __('nav.events') }}</flux:navbar.item>
                        <flux:navbar.item href="{{ localized_route('groups.index') }}" :current="request()->routeIs('groups.*')" class="font-bold text-lg">{{ __('nav.chapters') }}</flux:navbar.item>
                        <flux:navbar.item href="{{ localized_route('getting-started') }}" :current="request()->routeIs('getting-started')" class="font-bold text-lg">{{ __('nav.getting_started') }}</flux:navbar.item>
                        <flux:navbar.item href="{{ localized_route('volunteer') }}" :current="request()->routeIs('volunteer')" class="font-bold text-lg">{{ __('nav.help_out') }}</flux:navbar.item>
                        <flux:navbar.item href="{{ localized_route('about') }}" :current="request()->routeIs('about', 'about.*') || request()->routeIs('articles.*')" class="font-bold text-lg">{{ __('nav.about') }}</flux:navbar.item>
                    </flux:navbar>

                    <a href="{{ localized_route('membership') }}" class="steun-nav-btn">
                        <flux:icon name="heart" variant="solid" class="size-4" aria-hidden="true" />
                        {{ __('support.nav') }}
                    </a>

                    @auth
                        @foreach ($myChapters as $myChapter)
                            <a href="{{ localized_route('groups.roze-hesjes', ['group' => $myChapter]) }}"
                               class="roze-nav-btn {{ request()->routeIs('groups.roze-hesjes', 'groups.roze-hesjes.*', 'fr.groups.roze-hesjes', 'fr.groups.roze-hesjes.*') && optional(request()->route('group'))->is($myChapter) ? 'roze-nav-btn--active' : '' }}">
                                {{ \Illuminate\Support\Str::of($myChapter->name)->replaceMatches('/^\s*kidical\s+mass\s+/i', '')->trim() }}
                            </a>
                        @endforeach
                        <x-account-menu />
                    @endauth

                    <x-language-switch variant="desktop" />
                </div>

                <!-- Mobile: support CTA stays visible, links live behind the toggle -->
                <div class="site-nav__reveal-menu flex items-center gap-2 md:hidden">
                    <a href="{{ localized_route('membership') }}" class="steun-nav-btn">
                        <flux:icon name="heart" variant="solid" class="size-4" aria-hidden="true" />
                        {{ __('support.nav') }}
                    </a>
                    <flux:button icon="bars-3" variant="ghost" x-on:click="mobileOpen = !mobileOpen"
                                 aria-label="{{ __('nav.menu') }}"
                                 aria-expanded="false" x-bind:aria-expanded="mobileOpen.toString()"
                                 aria-controls="site-mobile-menu"
                                 class="mobile-menu-toggle" />
                </div>
            </div>

            <!-- Mobile dropdown panel -->
            <nav id="site-mobile-menu" aria-label="{{ __('nav.main_menu') }}" x-show="mobileOpen" x-transition class="site-nav__mobile-menu md:hidden">
                <x-language-switch variant="mobile" />
                @auth
                    @foreach ($myChapters as $myChapter)
                        <a href="{{ localized_route('groups.roze-hesjes', ['group' => $myChapter]) }}" class="roze-nav-btn roze-nav-btn--block mb-2">
                            {{ \Illuminate\Support\Str::of($myChapter->name)->replaceMatches('/^\s*kidical\s+mass\s+/i', '')->trim() }}
                        </a>
                    @endforeach
                @endauth
                <flux:navbar.item href="{{ localized_route('activities.index') }}" :current="request()->routeIs('activities.*')">{{ __('nav.events') }}</flux:navbar.item>
                <flux:navbar.item href="{{ localized_route('groups.index') }}" :current="request()->routeIs('groups.*')">{{ __('nav.chapters') }}</flux:navbar.item>
                <flux:navbar.item href="{{ localized_route('getting-started') }}" :current="request()->routeIs('getting-started')">{{ __('nav.getting_started') }}</flux:navbar.item>
                <flux:navbar.item href="{{ localized_route('volunteer') }}" :current="request()->routeIs('volunteer')">{{ __('nav.help_out') }}</flux:navbar.item>
                <flux:navbar.item href="{{ localized_route('about') }}" :current="request()->routeIs('about', 'about.*') || request()->routeIs('articles.*')">{{ __('nav.about') }}</flux:navbar.item>
            </nav>
        </div>
    </div>
</header>
