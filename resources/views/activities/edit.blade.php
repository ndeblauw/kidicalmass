<x-layouts::app :title="'Edit '.$activity->title_nl">
    <div class="mx-auto w-full max-w-4xl">
        <flux:card>
            <flux:heading size="lg">Edit activity</flux:heading>

            <form method="POST" action="{{ route('activities.update', $activity) }}" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                @include('activities._form')

                <div class="flex justify-end gap-2">
                    <flux:button variant="ghost" :href="route('activities.show', $activity)" wire:navigate>Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Save changes</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>
