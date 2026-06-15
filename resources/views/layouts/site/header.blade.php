@php
    // The visitor's chapters drive the roze nav button(s); compute once, reuse in both navs.
    $myChapters = Auth::check()
        ? Auth::user()->groups()->where('invisible', false)->orderBy('name')->get()
        : collect();
@endphp
<header class="site-header" x-data="{ mobileOpen: false }">
    <div class="container mx-auto px-4">
        <div class="site-nav">
            <div class="site-nav__bar flex items-center justify-between gap-4">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center">
                    <img
                        src="{{ asset('img/logos/logo.png') }}"
                        alt="Kidical Mass"
                        class="site-nav__logo w-auto"
                    >
                </a>

                <!-- Main Navigation -->
                <flux:navbar class="hidden md:flex">
                    <flux:navbar.item href="{{ route('activities.index') }}" :current="request()->routeIs('activities.*')" class="font-bold text-lg">{{ __('nav.events') }}</flux:navbar.item>
                    <flux:navbar.item href="{{ route('groups.index') }}" :current="request()->routeIs('groups.*')" class="font-bold text-lg">{{ __('nav.chapters') }}</flux:navbar.item>
                    <flux:navbar.item href="{{ route('getting-started') }}" :current="request()->routeIs('getting-started')" class="font-bold text-lg">{{ __('nav.getting_started') }}</flux:navbar.item>
                    <flux:navbar.item href="{{ route('volunteer') }}" :current="request()->routeIs('volunteer')" class="font-bold text-lg">{{ __('nav.help_out') }}</flux:navbar.item>
                    <flux:navbar.item href="{{ route('about') }}" :current="request()->routeIs('about', 'about.*') || request()->routeIs('articles.*')" class="font-bold text-lg">{{ __('nav.about') }}</flux:navbar.item>
                </flux:navbar>

                <!-- Support CTA + (for members) roze chapter button + account menu -->
                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('membership') }}" class="steun-nav-btn">
                        <flux:icon name="heart" variant="solid" class="size-4" aria-hidden="true" />
                        {{ __('support.nav') }}
                    </a>
                    @auth
                        @foreach ($myChapters as $myChapter)
                            <a href="{{ route('groups.roze-hesjes', $myChapter) }}"
                               class="roze-nav-btn {{ request()->routeIs('groups.roze-hesjes') && optional(request()->route('group'))->is($myChapter) ? 'roze-nav-btn--active' : '' }}">
                                {{ \Illuminate\Support\Str::of($myChapter->name)->replaceMatches('/^\s*kidical\s+mass\s+/i', '')->trim() }}
                            </a>
                        @endforeach
                        <flux:dropdown>
                            <flux:button variant="ghost" icon="user-circle" aria-label="Account" />
                            <flux:menu>
                                <flux:menu.item href="{{ route('profile.edit') }}" wire:navigate>Profile</flux:menu.item>
                                <flux:menu.item href="{{ route('user-password.edit') }}" wire:navigate>Password</flux:menu.item>
                                @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                                    <flux:menu.item href="{{ route('two-factor.show') }}" wire:navigate>Two-Factor Auth</flux:menu.item>
                                @endif
                                @if(Auth::user()->canAccessFilament())
                                    <flux:menu.separator />
                                    <flux:menu.item href="{{ url('/admin') }}">Admin</flux:menu.item>
                                @endif
                                <flux:menu.separator />
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <flux:menu.item type="submit">Logout</flux:menu.item>
                                </form>
                            </flux:menu>
                        </flux:dropdown>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <flux:button icon="bars-3" variant="ghost" class="md:hidden" x-on:click="mobileOpen = !mobileOpen" aria-label="Toggle menu" />
            </div>

            <!-- Mobile Navigation -->
            <nav x-show="mobileOpen" x-transition class="md:hidden pb-4 space-y-1">
                {{-- Support CTA pinned first, accent-styled (login lives in the footer) --}}
                <a href="{{ route('membership') }}" class="steun-nav-btn steun-nav-btn--block mb-2">
                    <flux:icon name="heart" variant="solid" class="size-4" aria-hidden="true" />
                    {{ __('support.nav') }}
                </a>
                @auth
                    @foreach ($myChapters as $myChapter)
                        <a href="{{ route('groups.roze-hesjes', $myChapter) }}" class="roze-nav-btn roze-nav-btn--block mb-2">
                            {{ \Illuminate\Support\Str::of($myChapter->name)->replaceMatches('/^\s*kidical\s+mass\s+/i', '')->trim() }}
                        </a>
                    @endforeach
                @endauth
                <flux:navbar.item href="{{ route('activities.index') }}" :current="request()->routeIs('activities.*')">{{ __('nav.events') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('groups.index') }}" :current="request()->routeIs('groups.*')">{{ __('nav.chapters') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('getting-started') }}" :current="request()->routeIs('getting-started')">{{ __('nav.getting_started') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('volunteer') }}" :current="request()->routeIs('volunteer')">{{ __('nav.help_out') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('about') }}" :current="request()->routeIs('about', 'about.*') || request()->routeIs('articles.*')">{{ __('nav.about') }}</flux:navbar.item>
            </nav>
        </div>
    </div>
</header>
