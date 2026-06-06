@props([
    'title' => 'Backstage',
    'group' => null,
    'volunteer' => null,
])

{{-- Backstage shell — the logged-in volunteer surface (D-1). A separate branded frontend,
     deliberately NOT the Filament admin. Calm, read-mostly, its own slim top bar so it reads
     as "my account", distinct from the public marketing site. PROTOTYPE. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — Kidical Mass</title>
    <meta name="robots" content="noindex">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito-sans:400,400i,700%7Cpoppins:800%7Ccaprasimo:400" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-kidical-light-yellow/40">

    {{-- Slim top bar --}}
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-kidical-ink/10">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between gap-4 h-20">

                <a href="{{ $group ? route('backstage.home', $group) : '#' }}" class="flex items-center gap-3 shrink-0 no-underline bg-none">
                    <img src="{{ asset('img/logos/logo.png') }}" alt="Kidical Mass" class="h-9 w-auto">
                    @if ($group)
                        <span class="hidden sm:inline-flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-kidical-ink/55">
                            <span class="h-4 w-px bg-kidical-ink/20"></span>
                            Backstage · {{ $group->name }}
                        </span>
                    @endif
                </a>

                @if ($group)
                    <nav class="hidden md:flex items-center gap-1 text-lg font-bold">
                        <a href="{{ route('backstage.home', $group) }}"
                           class="px-3 py-2 rounded-xl no-underline bg-none {{ request()->routeIs('backstage.home') ? 'bg-kidical-blue/10 text-kidical-blue' : 'text-kidical-ink/70 hover:text-kidical-ink' }}">Overzicht</a>
                        <a href="{{ route('backstage.team', $group) }}"
                           class="px-3 py-2 rounded-xl no-underline bg-none {{ request()->routeIs('backstage.team') ? 'bg-kidical-blue/10 text-kidical-blue' : 'text-kidical-ink/70 hover:text-kidical-ink' }}">Mijn team</a>
                    </nav>
                @endif

                <div class="flex items-center gap-3 shrink-0">
                    @if ($volunteer)
                        <span class="flex items-center justify-center size-10 rounded-full bg-kidical-red text-white font-bold text-sm" aria-hidden="true">{{ $volunteer->initials() }}</span>
                        <span class="hidden sm:block text-base font-bold text-kidical-ink leading-tight">{{ $volunteer->name }}</span>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-bold text-kidical-ink/55 hover:text-kidical-ink">Uitloggen</button>
                    </form>
                </div>

            </div>
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    @fluxScripts
    @stack('scripts')
</body>
</html>
