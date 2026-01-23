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

    <!-- Footer -->
    <footer class="bg-gradient-to-br from-blue-100 to-blue-50 text-gray-800 mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid md:grid-cols-2 gap-12">
                <!-- Left Column: Logo and Sponsors -->
                <div class="space-y-8">
                    <!-- Logo -->
                    <div class="flex items-center justify-center md:justify-start">
                        <img src="/img/logo-footer.png" alt="Kidical Mass Logo" class="max-w-xs w-full h-auto" />
                    </div>
                    
                    <!-- Sponsor Badges -->
                    <div class="space-y-4">
                        <!-- Brussel Mobiliteit Badge -->
                        <div class="bg-white rounded-xl border-2 border-green-600 p-4 max-w-sm">
                            <div class="flex items-center space-x-3">
                                <div class="text-green-600">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-2-13h4v6h-4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="inline-block bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold mb-1">
                                        MET DE STEUN VAN
                                    </div>
                                    <div class="font-bold text-green-700 text-sm">BRUSSEL MOBILITEIT</div>
                                    <div class="text-gray-600 text-xs uppercase tracking-wide">GEWESTELIJKE OVERHEIDSDIENST BRUSSEL</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Bruxelles Mobilité Badge -->
                        <div class="bg-white rounded-xl border-2 border-green-600 p-4 max-w-sm">
                            <div class="flex items-center space-x-3">
                                <div class="text-green-600">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-2-13h4v6h-4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="inline-block bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold mb-1">
                                        AVEC LE SOUTIEN DE
                                    </div>
                                    <div class="font-bold text-green-700 text-sm">BRUXELLES MOBILITÉ</div>
                                    <div class="text-gray-600 text-xs uppercase tracking-wide">SERVICE PUBLIC RÉGIONAL DE BRUXELLES</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Contact Information -->
                <div class="space-y-6">
                    <!-- Call to Action -->
                    <div>
                        <p class="text-lg font-bold text-gray-900 mb-1">Engagez-vous en tant que accompagnateur/co-organisateur !</p>
                        <p class="text-base font-semibold text-gray-800 mb-1">Wij zoeken nog begeleiders en lokale trekkers!</p>
                        <p class="text-base text-gray-700">Join your local group - mail: <a href="mailto:bike@kidicalmass.be" class="text-blue-700 hover:text-kidical-orange transition-colors underline font-semibold">bike@kidicalmass.be</a></p>
                    </div>
                    
                    <!-- Donation Information -->
                    <div>
                        <p class="text-lg font-bold text-gray-900 mb-1">Want to do a donation ?</p>
                        <p class="text-base text-gray-700">Kidical Mass Belgium (vzw) - BE72 8919 4405 3116 (VDK)</p>
                    </div>
                    
                    <!-- Sponsor Contact -->
                    <div>
                        <p class="text-base text-gray-700 mb-1">Want to be a sponsor ? <a href="mailto:contact@kidicalmass.brussels" class="text-blue-700 hover:text-kidical-orange transition-colors underline font-semibold">contact@kidicalmass.brussels</a></p>
                        <div class="flex flex-wrap gap-2 text-sm">
                            <a href="#" class="text-blue-700 hover:text-kidical-orange transition-colors underline">Sponsorformulas</a>
                            <span>-</span>
                            <a href="#" class="text-blue-700 hover:text-kidical-orange transition-colors underline">Sponsor & partner charter</a>
                        </div>
                    </div>
                    
                    <!-- Press Contact -->
                    <div>
                        <p class="text-lg font-bold text-gray-900 mb-1">Contact Presse/Pers</p>
                        <p class="text-base font-semibold text-gray-800 mb-1">Kidical Mass Belgium</p>
                        <p class="text-base text-gray-700 mb-1">Leticia Sere - coordination</p>
                        <p class="text-base text-gray-700 mb-1">Cecilia Pagola - PR - <a href="mailto:cecilia@kidicalmass.be" class="text-blue-700 hover:text-kidical-orange transition-colors underline">cecilia@kidicalmass.be</a></p>
                        <p class="text-base text-gray-700">0495 81 27 95 - <a href="mailto:bike@kidicalmass.be" class="text-blue-700 hover:text-kidical-orange transition-colors underline">bike@kidicalmass.be</a></p>
                    </div>
                    
                    <!-- Social Media -->
                    <div>
                        <p class="text-lg font-bold text-gray-900 mb-3">Follow us on:</p>
                        <div class="flex space-x-3">
                            <a href="#" class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 rounded-lg flex items-center justify-center transition-all shadow-md" aria-label="Instagram">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <a href="#" class="w-12 h-12 bg-blue-600 hover:bg-blue-700 rounded-lg flex items-center justify-center transition-colors shadow-md" aria-label="Facebook">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                            </a>
                            <a href="#" class="w-12 h-12 bg-blue-700 hover:bg-blue-800 rounded-lg flex items-center justify-center transition-colors shadow-md" aria-label="LinkedIn">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Original Footer Content - Quick Links Section -->
            <div class="border-t border-gray-300 mt-8 pt-8">
                <div class="grid md:grid-cols-4 gap-8">
                    <!-- About -->
                    <div>
                        <h3 class="text-lg font-bold mb-4 flex items-center text-kidical-blue">
                            <x-bike-icon class="w-5 h-5 mr-2" />
                            Kidical Mass Belgium
                        </h3>
                        <p class="text-gray-700 text-sm">
                            Safe, fun, and accessible cycling for families and children. Join us in creating a better future for our cities!
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="font-bold mb-4 text-kidical-blue">Quick Links</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('home') }}" class="text-gray-700 hover:text-kidical-orange transition-colors">Home</a></li>
                            <li><a href="{{ route('groups.index') }}" class="text-gray-700 hover:text-kidical-orange transition-colors">Groups</a></li>
                            <li><a href="{{ route('articles.index') }}" class="text-gray-700 hover:text-kidical-orange transition-colors">Articles</a></li>
                            <li><a href="{{ route('activities.index') }}" class="text-gray-700 hover:text-kidical-orange transition-colors">Activities</a></li>
                        </ul>
                    </div>

                    <!-- Get Involved -->
                    <div>
                        <h4 class="font-bold mb-4 text-kidical-blue">Get Involved</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-700 hover:text-kidical-orange transition-colors">Join a Group</a></li>
                            <li><a href="#" class="text-gray-700 hover:text-kidical-orange transition-colors">Organize an Event</a></li>
                            <li><a href="#" class="text-gray-700 hover:text-kidical-orange transition-colors">Volunteer</a></li>
                            <li><a href="#" class="text-gray-700 hover:text-kidical-orange transition-colors">Donate</a></li>
                        </ul>
                    </div>

                    <!-- Additional Contact -->
                    <div>
                        <h4 class="font-bold mb-4 text-kidical-blue">More Info</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="mailto:info@kidicalmass.be" class="text-gray-700 hover:text-kidical-orange transition-colors">info@kidicalmass.be</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-300 mt-8 pt-8 text-center text-sm text-gray-600">
                <p>&copy; {{ date('Y') }} Kidical Mass Belgium. All rights reserved.</p>
                <p class="mt-2">
                    <a href="#" class="hover:text-kidical-orange transition-colors">Privacy Policy</a> • 
                    <a href="#" class="hover:text-kidical-orange transition-colors">Terms of Service</a> • 
                    <a href="#" class="hover:text-kidical-orange transition-colors">Contact Us</a>
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