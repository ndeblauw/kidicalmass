@php($selectedGroups = collect(old('groups', $article->groups?->pluck('id')->all() ?? []))->map(fn ($id) => (string) $id)->all())

<div class="grid gap-4 md:grid-cols-2">
    <flux:input name="title_nl" label="Title (NL)" :value="old('title_nl', $article->title_nl)" required />
    <flux:input name="title_fr" label="Title (FR)" :value="old('title_fr', $article->title_fr)" required />
</div>

<flux:textarea name="content_nl" label="Content (NL)" rows="7" required>{{ old('content_nl', $article->content_nl) }}</flux:textarea>
<flux:textarea name="content_fr" label="Content (FR)" rows="7" required>{{ old('content_fr', $article->content_fr) }}</flux:textarea>

<flux:field>
    <flux:label>Groups</flux:label>
    <div class="mt-2 grid gap-2 md:grid-cols-2">
        @foreach ($groups as $group)
            <flux:checkbox
                name="groups[]"
                :value="$group->id"
                :label="$group->name"
                :checked="in_array((string) $group->id, $selectedGroups, true)"
            />
        @endforeach
    </div>
</flux:field>
