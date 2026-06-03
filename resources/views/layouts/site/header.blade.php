<header
    class="sticky top-0 z-50 bg-white shadow-sm"
    x-data="{ scrolled: false, mobileOpen: false }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
>
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between transition-all duration-300" :class="scrolled ? 'h-20' : 'h-28'">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center">
                <img
                    src="{{ asset('img/logo.png') }}"
                    alt="Kidical Mass"
                    class="w-auto transition-all duration-300"
                    :class="scrolled ? 'h-16' : 'h-[7.5rem]'"
                >
            </a>

            <!-- Main Navigation -->
            <flux:navbar class="hidden md:flex">
                <flux:navbar.item href="{{ route('activities.index') }}" :current="request()->routeIs('activities.*')" class="font-bold text-lg">{{ __('nav.events') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('groups.index') }}" :current="request()->routeIs('groups.*')" class="font-bold text-lg">{{ __('nav.chapters') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('getting-started') }}" :current="request()->routeIs('getting-started')" class="font-bold text-lg">{{ __('nav.getting_started') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('volunteer') }}" :current="request()->routeIs('volunteer')" class="font-bold text-lg">{{ __('nav.help_out') }}</flux:navbar.item>
                {{-- "Over ons" links straight to the /about hub (the hub page IS the
                     sub-page menu, with all six leaves as cards). A JS dropdown was inert
                     here: the public layout loads no Flux/Alpine scripts. This keeps
                     desktop consistent with mobile and works with zero JS. --}}
                <flux:navbar.item href="{{ route('about') }}" :current="request()->routeIs('about', 'about.*') || request()->routeIs('articles.*')" class="font-bold text-lg">{{ __('nav.about') }}</flux:navbar.item>
            </flux:navbar>

            <!-- Support CTA (replaces the login button; login moved to the footer) -->
            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('membership') }}" class="nav-support">
                    <flux:icon.heart variant="solid" class="nav-support__icon" aria-hidden="true" />
                    {{ __('support.nav') }}
                </a>
                @auth
                    <flux:dropdown>
                        <flux:button variant="ghost">{{ Auth::user()->name }}</flux:button>
                        <flux:menu>
                            <flux:menu.item href="{{ route('profile.edit') }}">Profile</flux:menu.item>
                            <flux:menu.separator />
                            <flux:menu.item href="{{ route('logout') }}" method="POST">Logout</flux:menu.item>
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
            <a href="{{ route('membership') }}" class="nav-support nav-support--mobile">
                <flux:icon.heart variant="solid" class="nav-support__icon" aria-hidden="true" />
                {{ __('support.nav') }}
            </a>
            <flux:navbar.item href="{{ route('activities.index') }}" :current="request()->routeIs('activities.*')">{{ __('nav.events') }}</flux:navbar.item>
            <flux:navbar.item href="{{ route('groups.index') }}" :current="request()->routeIs('groups.*')">{{ __('nav.chapters') }}</flux:navbar.item>
            <flux:navbar.item href="{{ route('getting-started') }}" :current="request()->routeIs('getting-started')">{{ __('nav.getting_started') }}</flux:navbar.item>
            <flux:navbar.item href="{{ route('volunteer') }}" :current="request()->routeIs('volunteer')">{{ __('nav.help_out') }}</flux:navbar.item>
            <flux:navbar.item href="{{ route('about') }}" :current="request()->routeIs('about', 'about.*') || request()->routeIs('articles.*')">{{ __('nav.about') }}</flux:navbar.item>
        </nav>
    </div>
</header>
