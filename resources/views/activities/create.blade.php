<x-layouts::app title="Create activity">
    <div class="mx-auto w-full max-w-4xl">
        <flux:card>
            <flux:heading size="lg">Create activity</flux:heading>

            <form method="POST" action="{{ route('activities.store') }}" class="mt-6 space-y-4">
                @csrf

                @include('activities._form')

                <div class="flex justify-end gap-2">
                    <flux:button variant="ghost" :href="route('activities.index')" wire:navigate>Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Create activity</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>
