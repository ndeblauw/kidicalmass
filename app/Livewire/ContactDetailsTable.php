<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class ContactDetailsTable extends Component
{
    public string $search = '';

    public string $groupFilter = '';

    public bool $showGroups = false;

    public bool $hasPhoneColumn = false;

    /** @var \Illuminate\Support\Collection<int, \App\Models\Group> */
    public Collection $groups;

    public function mount($group = null, mixed $groups = null): void
    {
        if ($group !== null) {
            $this->groups = collect([$group]);
            $this->groupFilter = (string) $group->id;
        } else {
            $this->groups = $groups;
        }

        $this->showGroups = $this->groups->count() > 1;
        $this->hasPhoneColumn = Schema::hasColumn('users', 'phone');
    }

    public function getRowsProperty(): EloquentCollection
    {
        $groupIds = $this->groups->pluck('id')->filter()->values();

        if ($groupIds->isEmpty()) {
            return User::query()->whereRaw('1 = 0')->get();
        }

        return User::query()
            ->whereHas('groups', function (Builder $query) use ($groupIds) {
                $query->whereIn('groups.id', $groupIds);

                if ($this->groupFilter !== '') {
                    $query->where('groups.id', $this->groupFilter);
                }
            })
            ->with(['groups' => fn ($query) => $query->whereIn('groups.id', $groupIds)->orderBy('name')])
            ->when($this->search !== '', function (Builder $query) {
                $searchTerm = '%'.$this->search.'%';

                $query->where(function (Builder $nested) use ($searchTerm) {
                    $nested->where('name', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm)
                        ->orWhereHas('groups', fn (Builder $groupQuery) => $groupQuery->where('name', 'like', $searchTerm));

                    if ($this->hasPhoneColumn) {
                        $nested->orWhere('phone', 'like', $searchTerm);
                    }
                });
            })
            ->orderBy('name')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.contact-details-table', [
            'rows' => $this->rows,
        ]);
    }
}
