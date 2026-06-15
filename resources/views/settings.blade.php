<x-layouts::site :title="__('Instellingen')">

    {{-- Pink hero header --}}
    <header class="chapter-head bg-kidical-red/40 mb-12">

        <div class="max-w-7xl mx-auto px-4 chapter-head__inner">
            <h1 class="text-5xl font-heading font-bold text-white">{{ __('Instellingen') }}</h1>
        </div>

    </header>

    <div class="mx-auto max-w-2xl space-y-16 py-4">
        {{-- Profile --}}
        <section>
            <h2 class="text-2xl font-bold text-kidical-ink">{{ __('Profiel') }}</h2>
            <p class="mt-1 text-kidical-ink/70">{{ __('Werk je naam en e-mailadres bij') }}</p>
            <div class="mt-6">
                <livewire:settings.profile />
            </div>
        </section>

        <hr class="border-kidical-ink">

        {{-- Password --}}
        <section>
            <h2 class="text-2xl font-bold text-kidical-ink">{{ __('Wachtwoord') }}</h2>
            <p class="mt-1 text-kidical-ink/70">{{ __('Zorg dat je account een lang, willekeurig wachtwoord gebruikt om veilig te blijven') }}</p>
            <div class="mt-6">
                <livewire:settings.password />
            </div>
        </section>

        {{-- Two-Factor Authentication --}}
        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            <section>
                <h2 class="text-2xl font-bold text-kidical-ink">{{ __('Tweestapsverificatie') }}</h2>
                <p class="mt-1 text-kidical-ink/70">{{ __('Beheer je tweestapsverificatie-instellingen') }}</p>
                <div class="mt-6">
                    <livewire:settings.two-factor />
                </div>
            </section>
        @endif
    </div>

</x-layouts::site>
