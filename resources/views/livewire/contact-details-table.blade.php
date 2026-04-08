<div class="space-y-4">
    <div class="grid gap-4 md:grid-cols-2">
        <flux:input
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            label="Search contacts"
            placeholder="Search by name, email, phone or group"
        />



        @if ($showGroups)
            <flux:select wire:model.live="groupFilter" label="Filter by group">
                <option value="">All groups</option>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </flux:select>
        @endif
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.cell>Name</flux:table.cell>
            <flux:table.cell>Email</flux:table.cell>
            <flux:table.cell>Telephone</flux:table.cell>
            @if ($showGroups)
                <flux:table.cell>Groups</flux:table.cell>
            @endif
        </flux:table.columns>

        @forelse ($rows as $user)
            <flux:table.row :key="$user->id">
                <flux:table.cell>{{ $user->name }}</flux:table.cell>
                <flux:table.cell>{{ $user->email }}</flux:table.cell>
                <flux:table.cell>{{ $hasPhoneColumn ? ($user->phone ?: '—') : '—' }}</flux:table.cell>
                @if ($showGroups)
                    <flux:table.cell>{{ $user->groups->pluck('name')->implode(', ') }}</flux:table.cell>
                @endif
            </flux:table.row>
        @empty
            <flux:table.row>
                <flux:table.cell colspan="{{ $showGroups ? 4 : 3 }}">No matching contacts found.</flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table>
</div>
