<x-layouts::app title="Create article">
    <div class="mx-auto w-full max-w-4xl">
        <flux:card>
            <flux:heading size="lg">Create news article</flux:heading>

            <form method="POST" action="{{ route('articles.store') }}" class="mt-6 space-y-4">
                @csrf

                @include('articles._form')

                <div class="flex justify-end gap-2">
                    <flux:button variant="ghost" :href="route('articles.index')" wire:navigate>Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Create article</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>
