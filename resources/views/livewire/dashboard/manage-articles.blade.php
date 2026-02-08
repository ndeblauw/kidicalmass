<div>
    <flux:card>
        <div class="flex items-center justify-between">
            <flux:heading size="lg">{{ __('Manage Articles') }}</flux:heading>
            @if(!$showForm)
                <flux:button wire:click="create" icon="plus">
                    {{ __('New Article') }}
                </flux:button>
            @endif
        </div>

        @if($showForm)
            <flux:separator class="my-4" />
            <form wire:submit.prevent="save" class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Title') }}</flux:label>
                    <flux:input wire:model="title" placeholder="{{ __('Enter article title') }}" />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Content') }}</flux:label>
                    <flux:textarea wire:model="content" rows="6" placeholder="{{ __('Enter article content') }}" />
                    <flux:error name="content" />
                </flux:field>

                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary">
                        {{ $editingArticleId ? __('Update Article') : __('Create Article') }}
                    </flux:button>
                    <flux:button wire:click="cancel" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                </div>
            </form>
        @endif
    </flux:card>

    @if($articles->count() > 0)
        <div class="mt-6 space-y-4">
            @foreach($articles as $article)
                <flux:card>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <flux:heading size="md">{{ $article->title }}</flux:heading>
                            <flux:subheading class="mt-1">
                                {{ __('By') }} {{ $article->author->name }} 
                                &bull; {{ $article->created_at->diffForHumans() }}
                            </flux:subheading>
                            <flux:text class="mt-2">{{ Str::limit($article->content, 200) }}</flux:text>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($article->groups as $group)
                                    <flux:badge>{{ $group->name }}</flux:badge>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <flux:button wire:click="edit({{ $article->id }})" size="sm" icon="pencil" variant="ghost">
                            </flux:button>
                            <flux:button 
                                wire:click="delete({{ $article->id }})" 
                                wire:confirm="{{ __('Are you sure you want to delete this article?') }}"
                                size="sm" 
                                icon="trash" 
                                variant="danger"
                            >
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @else
        @if(!$showForm)
            <flux:card class="mt-6">
                <div class="text-center py-8">
                    <flux:text>{{ __('No articles found. Create your first article!') }}</flux:text>
                </div>
            </flux:card>
        @endif
    @endif
</div>
