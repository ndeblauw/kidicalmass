<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between gap-4 rounded-lg border border-warning-600 bg-warning-50 dark:bg-warning-950 p-4">
            <div class="flex items-center gap-3">
                <x-filament::icon
                    icon="heroicon-o-exclamation-triangle"
                    class="h-6 w-6 text-warning-600 dark:text-warning-400"
                />
                <div>
                    <p class="font-semibold text-warning-900 dark:text-warning-100">
                        Impersonating User
                    </p>
                    <p class="text-sm text-warning-700 dark:text-warning-300">
                        You are currently logged in as <strong>{{ $this->getCurrentUserName() }}</strong>.
                        Original user: <strong>{{ $this->getOriginalUserName() }}</strong>
                    </p>
                </div>
            </div>
            <form action="{{ route('admin.impersonate.stop') }}" method="POST">
                @csrf
                <x-filament::button
                    type="submit"
                    color="danger"
                    size="sm"
                >
                    Stop Impersonating
                </x-filament::button>
            </form>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
