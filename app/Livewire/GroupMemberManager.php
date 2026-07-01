<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class GroupMemberManager extends Component
{
    public Group $group;

    public string $search = '';

    public function toggleRole(int $userId, string $role): void
    {
        $user = User::findOrFail($userId);

        abort_unless($this->group->users()->whereKey($user)->exists(), 403);

        $this->group->users()->updateExistingPivot($user, [
            'role' => $role === '' ? null : $role,
        ]);
    }

    #[Computed]
    public function users(): mixed
    {
        $search = $this->search;

        return $this->group->users()
            ->orderBy('name')
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->get();
    }

    public function render()
    {
        return view('livewire.group-member-manager');
    }
}
