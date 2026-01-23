<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kidical Mass Belgium' }}</title>
    <meta name="description" content="Kidical Mass Belgium - Safe and fun cycling for families and children">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white dark:bg-gray-900 flex flex-col">
    <!-- Header -->
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

    <!-- Main Content -->
    <main class="flex-1 container mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    <!-- Contact Form Section -->
    <section class="container mx-auto px-4 py-12">
        <livewire:contact-form-component />
    </section>

    <!-- Footer -->
    <footer class="bg-kidical-blue text-white mt-16">
        <div class="container mx-auto px-4 py-8">
            <div class="grid md:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <x-bike-icon class="w-5 h-5 mr-2" />
                        Kidical Mass Belgium
                    </h3>
                    <p class="text-white/80 text-sm">
                        Safe, fun, and accessible cycling for families and children. Join us in creating a better future for our cities!
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="text-white/80 hover:text-kidical-yellow transition-colors">Home</a></li>
                        <li><a href="{{ route('groups.index') }}" class="text-white/80 hover:text-kidical-yellow transition-colors">Groups</a></li>
                        <li><a href="{{ route('articles.index') }}" class="text-white/80 hover:text-kidical-yellow transition-colors">Articles</a></li>
                        <li><a href="{{ route('activities.index') }}" class="text-white/80 hover:text-kidical-yellow transition-colors">Activities</a></li>
                    </ul>
                </div>

                <!-- Get Involved -->
                <div>
                    <h4 class="font-bold mb-4">Get Involved</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-white/80 hover:text-kidical-yellow transition-colors">Join a Group</a></li>
                        <li><a href="#" class="text-white/80 hover:text-kidical-yellow transition-colors">Organize an Event</a></li>
                        <li><a href="#" class="text-white/80 hover:text-kidical-yellow transition-colors">Volunteer</a></li>
                        <li><a href="#" class="text-white/80 hover:text-kidical-yellow transition-colors">Donate</a></li>
                    </ul>
                </div>

                <!-- Contact & Social -->
                <div>
                    <h4 class="font-bold mb-4">Connect</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="mailto:info@kidicalmass.be" class="text-white/80 hover:text-kidical-yellow transition-colors">info@kidicalmass.be</a></li>
                        <li class="mt-4">
                            <div class="flex space-x-3">
                                <a href="#" class="w-8 h-8 bg-white/20 hover:bg-kidical-yellow rounded-full flex items-center justify-center transition-colors" aria-label="Facebook">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                                </a>
                                <a href="#" class="w-8 h-8 bg-white/20 hover:bg-kidical-yellow rounded-full flex items-center justify-center transition-colors" aria-label="Instagram">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                </a>
                                <a href="#" class="w-8 h-8 bg-white/20 hover:bg-kidical-yellow rounded-full flex items-center justify-center transition-colors" aria-label="Twitter">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-white/20 mt-8 pt-8 text-center text-sm text-white/80">
                <p>&copy; {{ date('Y') }} Kidical Mass Belgium. All rights reserved.</p>
                <p class="mt-2">
                    <a href="#" class="hover:text-kidical-yellow transition-colors">Privacy Policy</a> • 
                    <a href="#" class="hover:text-kidical-yellow transition-colors">Terms of Service</a> • 
                    <a href="#" class="hover:text-kidical-yellow transition-colors">Contact Us</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Toggle Script -->
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>