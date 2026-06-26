@props(['title' => null, 'description' => null, 'navChapter' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kidical Mass Belgium' }}</title>
    <meta name="description" content="{{ $description ?? 'Kidical Mass België: veilig en plezierig samen fietsen met kinderen, door je eigen buurt.' }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    {{-- Bunny needs families pipe-separated in ONE family= param; the repeated &family=
         syntax silently keeps only the first (so Poppins never loaded before this fix).
         display=swap shows the fallback immediately instead of blocking on the webfont. --}}
    <link href="https://fonts.bunny.net/css?family=nunito-sans:400,400i,700%7Cpoppins:800%7Ccaprasimo:400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
    @isset($navbar)
        {{ $navbar }}
    @else
        <x-layouts::site.header :chapter="$navChapter" />
    @endisset

    <!-- Main Content -->
    {{-- pt-28 clears the fixed floating nav pill (~5.5rem). Full-bleed blue bands
         (.home-hero/.chapter-head/.page-hero) cancel it with
         margin-top: calc(var(--spacing) * -28). --}}
    <main class="flex-1 container mx-auto px-4 {{ isset($navbar) ? 'pt-0' : 'pt-28' }} {{ isset($closing) ? 'pb-0' : 'pb-8' }}">
        {{ $slot }}
    </main>


    {{-- Page-owned closing block (e.g. <x-closing-cta>), rendered full-width directly
         above the footer zone. The page paints it yellow so it fuses with the zone. --}}
    @isset($closing)
        {{ $closing }}
    @endisset

    {{-- A page may supply its own footer (e.g. the roze-hesje hub's slim member
         footer) in place of the public marketing footer, mirroring $navbar above. --}}
    @isset($footer)
        {{ $footer }}
    @else
        <x-layouts::site.footer />
    @endisset

    @fluxScripts
    @stack('scripts')
</body>
</html>
