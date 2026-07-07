<x-layouts::auth :title="__('auth.verify_title')" :intro="__('auth.verify_intro')">
    @if (session('status') == 'verification-link-sent')
        <p class="mb-4 text-center font-medium text-kidical-green">
            {{ __('auth.verify_sent') }}
        </p>
    @endif

    <div class="flex flex-col items-center gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-cta-button type="submit" variant="yellow">
                {{ __('auth.verify_resend') }}
            </x-cta-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-cta-button type="submit" variant="ghost" size="sm" data-test="logout-button">
                {{ __('auth.logout') }}
            </x-cta-button>
        </form>
    </div>
</x-layouts::auth>
