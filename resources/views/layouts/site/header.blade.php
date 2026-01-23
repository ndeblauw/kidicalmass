<header class="bg-kidical-yellow shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo and Brand -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                <x-bike-icon class="w-8 h-8 text-kidical-blue transition-transform group-hover:scale-110" />
                <span class="text-2xl font-bold text-kidical-blue">Kidical Mass</span>
                <span class="hidden sm:inline-block text-sm text-kidical-blue/80 font-medium">Belgium</span>
            </a>

            <!-- Main Navigation -->
            <nav class="hidden md:flex items-center space-x-1">
                <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg text-kidical-blue hover:bg-kidical-orange hover:text-white transition-colors font-medium {{ request()->routeIs('home') ? 'bg-kidical-orange text-white' : '' }}">
                    Home
                </a>
                <a href="{{ route('groups.index') }}" class="px-4 py-2 rounded-lg text-kidical-blue hover:bg-kidical-orange hover:text-white transition-colors font-medium {{ request()->routeIs('groups.*') ? 'bg-kidical-orange text-white' : '' }}">
                    Groups
                </a>
                <a href="{{ route('articles.index') }}" class="px-4 py-2 rounded-lg text-kidical-blue hover:bg-kidical-orange hover:text-white transition-colors font-medium {{ request()->routeIs('articles.*') ? 'bg-kidical-orange text-white' : '' }}">
                    Articles
                </a>
                <a href="{{ route('activities.index') }}" class="px-4 py-2 rounded-lg text-kidical-blue hover:bg-kidical-orange hover:text-white transition-colors font-medium {{ request()->routeIs('activities.*') ? 'bg-kidical-orange text-white' : '' }}">
                    Activities
                </a>
            </nav>

            <!-- User Menu -->
            <div class="flex items-center space-x-2">
                @guest
                    <a href="{{ route('login') }}" class="px-4 py-2 text-kidical-blue hover:text-kidical-orange transition-colors font-medium">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-kidical-green text-white rounded-lg hover:bg-kidical-blue transition-colors font-medium">
                        Register
                    </a>
                @else
                    <flux:dropdown>
                        <flux:button variant="ghost" size="sm" class="text-kidical-blue">
                            {{ Auth::user()->name }}
                        </flux:button>
                        <flux:menu>
                            <flux:menu.item href="{{ route('profile.edit') }}">
                                Profile
                            </flux:menu.item>
                            <flux:menu.separator />
                            <flux:menu.item href="{{ route('logout') }}" method="POST">
                                Logout
                            </flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                @endguest
            </div>

            <!-- Mobile Menu Button -->
            <button class="md:hidden p-2 text-kidical-blue hover:text-kidical-orange transition-colors" onclick="toggleMobileMenu()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <nav id="mobileMenu" class="hidden md:hidden pb-4 space-y-2">
            <a href="{{ route('home') }}" class="block px-4 py-2 rounded-lg text-kidical-blue hover:bg-kidical-orange hover:text-white transition-colors font-medium {{ request()->routeIs('home') ? 'bg-kidical-orange text-white' : '' }}">
                Home
            </a>
            <a href="{{ route('groups.index') }}" class="block px-4 py-2 rounded-lg text-kidical-blue hover:bg-kidical-orange hover:text-white transition-colors font-medium {{ request()->routeIs('groups.*') ? 'bg-kidical-orange text-white' : '' }}">
                Groups
            </a>
            <a href="{{ route('articles.index') }}" class="block px-4 py-2 rounded-lg text-kidical-blue hover:bg-kidical-orange hover:text-white transition-colors font-medium {{ request()->routeIs('articles.*') ? 'bg-kidical-orange text-white' : '' }}">
                Articles
            </a>
            <a href="{{ route('activities.index') }}" class="block px-4 py-2 rounded-lg text-kidical-blue hover:bg-kidical-orange hover:text-white transition-colors font-medium {{ request()->routeIs('activities.*') ? 'bg-kidical-orange text-white' : '' }}">
                Activities
            </a>
        </nav>
    </div>
</header>
