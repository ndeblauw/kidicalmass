<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kidical Mass Belgium' }}</title>
    <meta name="description" content="Kidical Mass Belgium - Safe and fun cycling for families and children">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito-sans:400,400i,700&family=poppins:800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
    <x-layouts::site.header />

    <!-- Main Content -->
    <main class="flex-1 container mx-auto px-4 py-8">
        {{ $slot }}
    </main>


    <!-- Contact Form Section -->
    <section class="container mx-auto px-4 py-12">
        <livewire:contact-form-component />
    </section>

    <!-- Partners/Sponsors Section -->
    <x-partners />

    <x-layouts::site.footer />

    @stack('scripts')
</body>
</html>
