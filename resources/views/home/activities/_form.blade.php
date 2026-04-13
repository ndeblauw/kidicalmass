@php($selectedGroups = collect(old('groups', $activity->groups?->pluck('id')->all() ?? []))->map(fn ($id) => (string) $id)->all())

<div class="grid gap-4 md:grid-cols-2">
    <flux:input name="title_nl" label="Title (NL)" :value="old('title_nl', $activity->title_nl)" required />
    <flux:input name="title_fr" label="Title (FR)" :value="old('title_fr', $activity->title_fr)" required />
</div>

<div class="grid gap-4 md:grid-cols-2">
    <flux:select name="activity_type" label="Activity type" required>
        @foreach (\App\Enums\ActivityType::cases() as $type)
            <option value="{{ $type->value }}" @selected(old('activity_type', $activity->activity_type?->value) === $type->value)>
                {{ $type->label() }}
            </option>
        @endforeach
    </flux:select>
    <flux:input name="location" label="Location" :value="old('location', $activity->location)" required />
</div>

<div class="grid gap-4 md:grid-cols-2">
    <flux:select name="organizer_id" label="Organizer">
        <option value="">— Select organizer —</option>
        @foreach ($users as $user)
            <option value="{{ $user->id }}" @selected(old('organizer_id', $activity->organizer_id ?? ($activity->exists ? null : auth()->id())) == $user->id)>
                {{ $user->name }}
            </option>
        @endforeach
    </flux:select>
    <flux:select name="author_id" label="Author" required>
        @foreach ($users as $user)
            <option value="{{ $user->id }}" @selected(old('author_id', $activity->author_id ?? ($activity->exists ? null : auth()->id())) == $user->id)>
                {{ $user->name }}
            </option>
        @endforeach
    </flux:select>
</div>

<div class="grid gap-4 md:grid-cols-2">
    <flux:input name="begin_date" type="datetime-local" label="Begin date" :value="old('begin_date', optional($activity->begin_date)->format('Y-m-d\TH:i'))" required />
    <flux:input name="duration_minutes" type="number" label="Duration (minutes)" :value="old('duration_minutes', $activity->duration_minutes)" />
</div>

<flux:input name="commute_link" label="Commute link" :value="old('commute_link', $activity->commute_link)" placeholder="https://" />

<flux:textarea name="content_nl" label="Content (NL)" rows="5" required>{{ old('content_nl', $activity->content_nl) }}</flux:textarea>
<flux:textarea name="content_fr" label="Content (FR)" rows="5" required>{{ old('content_fr', $activity->content_fr) }}</flux:textarea>

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

@if($activity->getFirstMedia('main'))
    <flux:field>
        <flux:label>Current image</flux:label>
        <div class="mt-2">
            <img src="{{ $activity->getFirstMediaUrl('main', 'thumb') }}" class="w-24 h-24 object-cover rounded-lg" />
        </div>
    </flux:field>
@endif

<flux:field>
    <flux:label>Image</flux:label>
    <flux:input name="image" type="file" accept="image/*" />
</flux:field>
