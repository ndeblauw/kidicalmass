<x-layouts::app :title="'Edit '.$article->title_nl">
    <div class="mx-auto w-full max-w-4xl">
        <flux:card>
            <flux:heading size="lg">Edit news article</flux:heading>

            <form method="POST" action="{{ route('articles.update', $article) }}" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                @include('articles._form')

                <div class="flex justify-end gap-2">
                    <flux:button variant="ghost" :href="route('articles.show', $article)" wire:navigate>Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Save changes</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>
