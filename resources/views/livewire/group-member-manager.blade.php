<div>
    <div class="mb-4">
        <input type="text" wire:model.live="search" placeholder="Search by name or email..."
               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Interested</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Pink Vest</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Captain</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Public</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Last Active</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($this->users as $user)
                    <tr class="hover:bg-gray-50" wire:key="user-{{ $user->id }}">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            <a href="mailto:{{ $user->email }}" class="text-blue-600 hover:text-blue-800">{{ $user->email }}</a>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="radio"
                                   name="role-{{ $user->id }}"
                                   value="interested"
                                   wire:click="toggleRole({{ $user->id }}, '')"
                                   @checked(is_null($user->pivot->role))
                                   class="h-4 w-4 border-gray-300 text-gray-400 focus:ring-gray-300">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="radio"
                                   name="role-{{ $user->id }}"
                                   value="pinkvest"
                                   wire:click="toggleRole({{ $user->id }}, 'pinkvest')"
                                   @checked($user->pivot->role === 'pinkvest')
                                   class="h-4 w-4 border-gray-300 text-pink-500 focus:ring-pink-400">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="radio"
                                   name="role-{{ $user->id }}"
                                   value="captain"
                                   wire:click="toggleRole({{ $user->id }}, 'captain')"
                                   @checked($user->pivot->role === 'captain')
                                   class="h-4 w-4 border-gray-300 text-amber-500 focus:ring-amber-400">
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($user->pivot->is_public)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">yes</span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">no</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-sm text-gray-500">
                            {{ $user->last_active_on?->diffForHumans() ?: '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                            No members found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
