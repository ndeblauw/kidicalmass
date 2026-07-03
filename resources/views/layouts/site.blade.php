@props(['title' => null, 'description' => null, 'ogImage' => null, 'ogType' => 'website', 'navChapter' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.site-head')
</head>
<body class="min-h-screen flex flex-col ba-site">
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
