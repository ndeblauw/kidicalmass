@props(['title' => null, 'intro' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.site-head')
    </head>
    <body class="auth-page min-h-svh bg-kidical-light-yellow antialiased">
        <div class="auth-page__grid min-h-svh p-6 md:p-10">
            <x-photo-collage
                class="auth-page__collage"
                :photos="[
                    ['src' => 'img/photography/volunteers-pink-vest-group-cobbles.webp', 'alt' => __('auth.collage_alt_group')],
                    ['src' => 'img/photography/ride-trio-pink-vest-lei-portrait.webp', 'alt' => __('auth.collage_alt_trio'), 'pos' => '60% 30%'],
                    ['src' => 'img/photography/volunteer-pink-vest.webp', 'alt' => __('auth.collage_alt_vest'), 'pos' => 'center 20%'],
                ]" />

            <div class="auth-page__form">
                <div class="auth-page__logo-row">
                    <a href="{{ route('home') }}">
                        <img class="auth-page__logo" src="{{ asset('img/logos/logo.png') }}" alt="{{ __('auth.logo_alt') }}">
                    </a>
                    <span class="auth-page__role-pill">{{ __('auth.role_pill') }}</span>
                </div>

                @if ($title)
                    <h1 class="auth-page__title">{{ $title }}</h1>
                @endif

                @if ($intro)
                    <p class="auth-page__intro">{{ $intro }}</p>
                @endif

                {{ $slot }}
            </div>
        </div>

        @fluxScripts
        @stack('scripts')
    </body>
</html>
