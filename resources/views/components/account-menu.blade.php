<flux:dropdown>
    <flux:button variant="ghost" icon="ellipsis-vertical" aria-label="{{ __('nav.account') }}" class="account-nav-btn" />
    <flux:menu>
        <flux:menu.item href="{{ route('settings') }}" wire:navigate>{{ __('nav.settings') }}</flux:menu.item>
        @if(Auth::user()->canAccessFilament())
            <flux:menu.separator />
            <flux:menu.item href="{{ url('/admin') }}">{{ __('nav.admin') }}</flux:menu.item>
        @endif
        <flux:menu.separator />
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <flux:menu.item type="submit">{{ __('auth.logout') }}</flux:menu.item>
        </form>
    </flux:menu>
</flux:dropdown>
