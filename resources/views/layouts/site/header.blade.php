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
                <flux:navbar.item href="{{ route('groups.index') }}" :current="request()->routeIs('groups.*')" class="font-bold text-lg">Groups</flux:navbar.item>
                <flux:navbar.item href="{{ route('articles.index') }}" :current="request()->routeIs('articles.*')" class="font-bold text-lg">Articles</flux:navbar.item>
                <flux:navbar.item href="{{ route('activities.index') }}" :current="request()->routeIs('activities.*')" class="font-bold text-lg">Activities</flux:navbar.item>
            </flux:navbar>

            <!-- User Menu -->
            <div class="hidden md:flex items-center gap-2">
                @guest
                    <flux:button href="{{ route('login') }}" variant="ghost">Login</flux:button>
                    <flux:button href="{{ route('register') }}">Register</flux:button>
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
            <flux:navbar.item href="{{ route('groups.index') }}" :current="request()->routeIs('groups.*')">Groups</flux:navbar.item>
            <flux:navbar.item href="{{ route('articles.index') }}" :current="request()->routeIs('articles.*')">Articles</flux:navbar.item>
            <flux:navbar.item href="{{ route('activities.index') }}" :current="request()->routeIs('activities.*')">Activities</flux:navbar.item>
            @guest
                <flux:navbar.item href="{{ route('login') }}">Login</flux:navbar.item>
                <flux:navbar.item href="{{ route('register') }}">Register</flux:navbar.item>
            @endguest
        </nav>
    </div>
</header>
