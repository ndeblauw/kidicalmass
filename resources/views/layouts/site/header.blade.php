<header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-1 group transition-transform group-hover:scale-105">
                <!-- Daisy icon (inline SVG) -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" class="h-12 w-auto" aria-hidden="true">
                    <g transform="translate(40,40)">
                        <ellipse cx="0" cy="-27" rx="8" ry="15" fill="#FDB913" transform="rotate(0)" />
                        <ellipse cx="0" cy="-27" rx="8" ry="15" fill="#FDB913" transform="rotate(30)" />
                        <ellipse cx="0" cy="-27" rx="8" ry="15" fill="#FDB913" transform="rotate(60)" />
                        <ellipse cx="0" cy="-27" rx="8" ry="15" fill="#FDB913" transform="rotate(90)" />
                        <ellipse cx="0" cy="-27" rx="8" ry="15" fill="#FDB913" transform="rotate(120)" />
                        <ellipse cx="0" cy="-27" rx="8" ry="15" fill="#FDB913" transform="rotate(150)" />
                        <ellipse cx="0" cy="-27" rx="8" ry="15" fill="#FDB913" transform="rotate(180)" />
                        <ellipse cx="0" cy="-27" rx="8" ry="15" fill="#FDB913" transform="rotate(210)" />
                        <ellipse cx="0" cy="-27" rx="8" ry="15" fill="#FDB913" transform="rotate(240)" />
                        <ellipse cx="0" cy="-27" rx="8" ry="15" fill="#FDB913" transform="rotate(270)" />
                        <ellipse cx="0" cy="-27" rx="8" ry="15" fill="#FDB913" transform="rotate(300)" />
                        <ellipse cx="0" cy="-27" rx="8" ry="15" fill="#FDB913" transform="rotate(330)" />
                        <circle cx="0" cy="0" r="17" fill="white" />
                        <circle cx="0" cy="0" r="15" fill="#FFF9E6" />
                        <circle cx="-5" cy="-4" r="2.2" fill="#333" />
                        <circle cx="5" cy="-4" r="2.2" fill="#333" />
                        <path d="M -5 3 Q 0 8 5 3" stroke="#333" stroke-width="1.8" fill="none" stroke-linecap="round" />
                    </g>
                </svg>
                <!-- Brand text -->
                <div class="logo-brand-text">
                    <div class="text-xl">KIDICAL</div>
                    <div class="text-2xl">MASS</div>
                </div>
            </a>

            <!-- Main Navigation -->
            <nav class="hidden md:flex items-center space-x-1">
                <a href="{{ route('home') }}" class="px-4 py-2 rounded-full text-black hover:bg-kidical-yellow hover:text-black transition-colors font-semibold {{ request()->routeIs('home') ? 'bg-kidical-yellow text-black' : '' }}">
                    Home
                </a>
                <a href="{{ route('groups.index') }}" class="px-4 py-2 rounded-full text-black hover:bg-kidical-red hover:text-white transition-colors font-semibold {{ request()->routeIs('groups.*') ? 'bg-kidical-red text-white' : '' }}">
                    Groups
                </a>
                <a href="{{ route('articles.index') }}" class="px-4 py-2 rounded-full text-black hover:bg-kidical-yellow hover:text-black transition-colors font-semibold {{ request()->routeIs('articles.*') ? 'bg-kidical-yellow text-black' : '' }}">
                    Articles
                </a>
                <a href="{{ route('activities.index') }}" class="px-4 py-2 rounded-full text-black hover:bg-kidical-red hover:text-white transition-colors font-semibold {{ request()->routeIs('activities.*') ? 'bg-kidical-red text-white' : '' }}">
                    Activities
                </a>
                <a href="{{ route('press.index') }}" class="px-4 py-2 rounded-full text-black hover:bg-kidical-yellow hover:text-black transition-colors font-semibold {{ request()->routeIs('press.*') ? 'bg-kidical-yellow text-black' : '' }}">
                    Press
                </a>
            </nav>

            <!-- User Menu -->
            <div class="flex items-center space-x-2">
                @guest
                    <a href="{{ route('login') }}" class="px-4 py-2 text-black hover:text-kidical-red transition-colors font-semibold">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-kidical-yellow text-black rounded-full hover:bg-kidical-red hover:text-white transition-colors font-semibold">
                        Register
                    </a>
                @else
                    <flux:dropdown>
                        <flux:button variant="ghost" size="sm" class="text-black">
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
            <button class="md:hidden p-2 text-black hover:text-kidical-red transition-colors" onclick="toggleMobileMenu()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <nav id="mobileMenu" class="hidden md:hidden pb-4 space-y-2">
            <a href="{{ route('home') }}" class="block px-4 py-2 rounded-full text-black hover:bg-kidical-yellow hover:text-black transition-colors font-semibold {{ request()->routeIs('home') ? 'bg-kidical-yellow text-black' : '' }}">
                Home
            </a>
            <a href="{{ route('groups.index') }}" class="block px-4 py-2 rounded-full text-black hover:bg-kidical-red hover:text-white transition-colors font-semibold {{ request()->routeIs('groups.*') ? 'bg-kidical-red text-white' : '' }}">
                Groups
            </a>
            <a href="{{ route('articles.index') }}" class="block px-4 py-2 rounded-full text-black hover:bg-kidical-yellow hover:text-black transition-colors font-semibold {{ request()->routeIs('articles.*') ? 'bg-kidical-yellow text-black' : '' }}">
                Articles
            </a>
            <a href="{{ route('activities.index') }}" class="block px-4 py-2 rounded-full text-black hover:bg-kidical-red hover:text-white transition-colors font-semibold {{ request()->routeIs('activities.*') ? 'bg-kidical-red text-white' : '' }}">
                Activities
            </a>
            <a href="{{ route('press.index') }}" class="block px-4 py-2 rounded-full text-black hover:bg-kidical-yellow hover:text-black transition-colors font-semibold {{ request()->routeIs('press.*') ? 'bg-kidical-yellow text-black' : '' }}">
                Press
            </a>
        </nav>
    </div>
</header>
