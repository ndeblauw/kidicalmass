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

                <!-- Support CTA (replaces the login button; login moved to the footer) -->
                <div class="hidden md:flex items-center gap-3">
                    <x-cta-button :href="route('membership')" icon="heart" size="sm">{{ __('support.nav') }}</x-cta-button>
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
                <x-cta-button :href="route('membership')" icon="heart" size="sm" class="cta-button--block mb-2">{{ __('support.nav') }}</x-cta-button>
                <flux:navbar.item href="{{ route('activities.index') }}" :current="request()->routeIs('activities.*')">{{ __('nav.events') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('groups.index') }}" :current="request()->routeIs('groups.*')">{{ __('nav.chapters') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('getting-started') }}" :current="request()->routeIs('getting-started')">{{ __('nav.getting_started') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('volunteer') }}" :current="request()->routeIs('volunteer')">{{ __('nav.help_out') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('about') }}" :current="request()->routeIs('about', 'about.*') || request()->routeIs('articles.*')">{{ __('nav.about') }}</flux:navbar.item>
            </nav>
        </div>
    </div>
</header>
