<flux:dropdown>
    <flux:button variant="ghost" icon="ellipsis-vertical" aria-label="Account" class="account-nav-btn" />
    <flux:menu>
        <flux:menu.item href="{{ route('settings') }}" wire:navigate>{{ __('Instellingen') }}</flux:menu.item>
        @if(Auth::user()->canAccessFilament())
            <flux:menu.separator />
            <flux:menu.item href="{{ url('/admin') }}">{{ __('Admin') }}</flux:menu.item>
        @endif
        <flux:menu.separator />
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <flux:menu.item type="submit">{{ __('Uitloggen') }}</flux:menu.item>
        </form>
    </flux:menu>
</flux:dropdown>
