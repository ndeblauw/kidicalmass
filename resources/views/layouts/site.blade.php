<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kidical Mass' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <flux:navbar class="border-b border-gray-200 dark:border-gray-700">
        <flux:brand href="{{ route('home') }}">
            Kidical Mass
        </flux:brand>
        
        <flux:navbar.item href="{{ route('groups.index') }}">
            Groups
        </flux:navbar.item>
        <flux:navbar.item href="{{ route('articles.index') }}">
            Articles
        </flux:navbar.item>
        <flux:navbar.item href="{{ route('activities.index') }}">
            Activities
        </flux:navbar.item>

        <flux:spacer />

        @guest
            <flux:navbar.item href="{{ route('login') }}">
                Login
            </flux:navbar.item>
            <flux:navbar.item href="{{ route('register') }}">
                Register
            </flux:navbar.item>
        @else
            <flux:dropdown>
                <flux:button variant="ghost" size="sm">
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
    </flux:navbar>

    <main class="container mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-16">
        <div class="container mx-auto px-4 py-6">
            <div class="text-center text-gray-600 dark:text-gray-400">
                <p>&copy; {{ date('Y') }} Kidical Mass. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>