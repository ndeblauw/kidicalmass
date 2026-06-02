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
                <flux:dropdown>
                    <flux:navbar.item icon:trailing="chevron-down" :current="request()->routeIs('about.*') || request()->routeIs('articles.*')" class="font-bold text-lg">{{ __('nav.about') }}</flux:navbar.item>
                    <flux:menu>
                        <flux:menu.item href="{{ route('about.mission') }}">{{ __('nav.mission') }}</flux:menu.item>
                        <flux:menu.item href="{{ route('about.vision') }}">{{ __('nav.vision') }}</flux:menu.item>
                        <flux:menu.item href="{{ route('about.organisation') }}">{{ __('nav.organisation') }}</flux:menu.item>
                        <flux:menu.item href="{{ route('articles.index') }}">{{ __('nav.news') }}</flux:menu.item>
                        <flux:menu.item href="{{ route('about.press') }}">{{ __('nav.press') }}</flux:menu.item>
                        <flux:menu.item href="{{ route('about.partners') }}">{{ __('nav.partners') }}</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </flux:navbar>

            <!-- User Menu -->
            <div class="hidden md:flex items-center gap-2">
                @guest
                    <flux:button href="{{ route('login') }}" variant="ghost">{{ __('nav.login') }}</flux:button>
                @else
                    <flux:dropdown>
                        <flux:button variant="ghost">{{ Auth::user()->name }}</flux:button>
                        <flux:menu>
                            <flux:menu.item href="{{ route('profile.edit') }}">Profile</flux:menu.item>
                            <flux:menu.separator />
                            <flux:menu.item href="{{ route('logout') }}" method="POST">Logout</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                @endguest
            </div>

            <!-- Mobile Menu Button -->
            <flux:button icon="bars-3" variant="ghost" class="md:hidden" x-on:click="mobileOpen = !mobileOpen" aria-label="Toggle menu" />
        </div>

        <!-- Mobile Navigation -->
        <nav x-show="mobileOpen" x-transition class="md:hidden pb-4 space-y-1">
            <flux:navbar.item href="{{ route('activities.index') }}" :current="request()->routeIs('activities.*')">{{ __('nav.events') }}</flux:navbar.item>
            <flux:navbar.item href="{{ route('groups.index') }}" :current="request()->routeIs('groups.*')">{{ __('nav.chapters') }}</flux:navbar.item>
            <flux:navbar.item href="{{ route('getting-started') }}" :current="request()->routeIs('getting-started')">{{ __('nav.getting_started') }}</flux:navbar.item>
            <flux:navbar.item href="{{ route('volunteer') }}" :current="request()->routeIs('volunteer')">{{ __('nav.help_out') }}</flux:navbar.item>
            <flux:navbar.item href="{{ route('about.mission') }}" :current="request()->routeIs('about.*')">{{ __('nav.about') }}</flux:navbar.item>
            @guest
                <flux:navbar.item href="{{ route('login') }}">{{ __('nav.login') }}</flux:navbar.item>
            @endguest
        </nav>
    </div>
</header>
