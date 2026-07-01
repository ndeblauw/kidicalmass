<x-layouts::site :title="__('Instellingen')">

    {{-- Member workspace chrome (no chapter sub-nav): settings is account-level, but it
         should wear the same calm white shell as the roze-hesje hub, not the red
         marketing layout. Mirrors <x-roze-hub>'s navbar/footer slots. --}}
    <x-slot:navbar>
        <div class="roze-chrome">
            <div class="roze-shell-bar">
                <div class="roze-shell-bar__inner">
                    <a href="{{ route('home') }}" class="roze-shell-bar__logo" aria-label="Terug naar Kidical Mass">
                        <img src="{{ asset('img/logos/logo-icon.png') }}" alt="Kidical Mass" class="roze-shell-bar__mark">
                    </a>
                    <span class="roze-shell-bar__context">
                        <span class="roze-shell-bar__place">{{ auth()->user()->name }}</span>
                        <span class="roze-shell-bar__role">account</span>
                    </span>
                    <div class="roze-shell-bar__account">
                        <x-account-menu />
                    </div>
                </div>
            </div>
        </div>
    </x-slot:navbar>

    <div class="roze-hub-body">
        <h1 class="roze-hub-title">{{ __('Instellingen') }}</h1>

        <div class="mt-8 space-y-12">
            {{-- Profile --}}
            <section>
                <h2 class="roze-hub-subtitle">{{ __('Profiel') }}</h2>
                <p class="roze-hub-lead">{{ __('Werk je naam en e-mailadres bij') }}</p>
                <livewire:settings.profile />
            </section>

            {{-- Password --}}
            <section>
                <h2 class="roze-hub-subtitle">{{ __('Wachtwoord') }}</h2>
                <p class="roze-hub-lead">{{ __('Zorg dat je account een lang, willekeurig wachtwoord gebruikt om veilig te blijven') }}</p>
                <livewire:settings.password />
            </section>

            {{-- Two-Factor Authentication --}}
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <section>
                    <h2 class="roze-hub-subtitle">{{ __('Tweestapsverificatie') }}</h2>
                    <p class="roze-hub-lead">{{ __('Beheer je tweestapsverificatie-instellingen') }}</p>
                    <livewire:settings.two-factor />
                </section>
            @endif
        </div>
    </div>

    {{-- Slim member footer, matching the hub: a calm way back to the public site
         and a help hand-off (no chapter place, since settings is account-wide). --}}
    <x-slot:footer>
        <footer class="roze-foot">
            <div class="roze-foot__inner">
                <a href="{{ route('home') }}" class="roze-foot__home">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    <span>Terug naar de website</span>
                </a>
                <span class="roze-foot__meta">
                    <a href="{{ route('contact') }}" class="roze-foot__help">Hulp nodig?</a>
                </span>
            </div>
        </footer>
    </x-slot:footer>

</x-layouts::site>
