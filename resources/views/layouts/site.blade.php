<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kidical Mass Belgium' }}</title>
    <meta name="description" content="Kidical Mass Belgium - Safe and fun cycling for families and children">
    <link rel="preconnect" href="https://fonts.bunny.net">
    {{-- Bunny needs families pipe-separated in ONE family= param; the repeated &family=
         syntax silently keeps only the first (so Poppins never loaded before this fix). --}}
    <link href="https://fonts.bunny.net/css?family=nunito-sans:400,400i,700%7Cpoppins:800%7Ccaprasimo:400" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
    <x-layouts::site.header />

    <!-- Main Content -->
    {{-- pt-28 clears the fixed floating nav pill (~5.5rem). Full-bleed blue bands
         (.home-hero/.activity-hero/.chapter-head/.page-hero__spacer) cancel it with
         margin-top: calc(var(--spacing) * -28). --}}
    <main class="flex-1 container mx-auto px-4 pb-8 pt-28">
        {{ $slot }}
    </main>


    {{-- Partner recognition strip (PAT-5, slim). Full story on /about/partners. --}}
    <x-partners />

    <x-layouts::site.footer />

    @fluxScripts
    @stack('scripts')
</body>
</html>
