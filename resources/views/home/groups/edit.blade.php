<x-layouts::app :title="'Edit '.$group->name">
    <div class="mx-auto w-full max-w-2xl">
        <flux:card>
            <flux:heading size="lg">Edit group details</flux:heading>

            <form method="POST" action="{{ route('home.groups.update', $group) }}" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                <flux:input
                    name="name"
                    label="Group name"
                    :value="old('name', $group->name)"
                    required
                />

                <flux:input
                    name="zip"
                    label="ZIP code"
                    :value="old('zip', $group->zip)"
                />

                <div class="flex justify-end gap-2">
                    <flux:button variant="ghost" :href="route('home.groups.show', $group)" wire:navigate>
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        Save changes
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>
