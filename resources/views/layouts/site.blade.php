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
    <x-layouts::site.header />

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

    <!-- Partners/Sponsors Section -->
    <x-partners />

    <x-layouts::site.footer />


    <!-- Mobile Menu Toggle Script -->
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>
