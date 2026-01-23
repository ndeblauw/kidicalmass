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
