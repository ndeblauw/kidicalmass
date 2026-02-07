<?php

namespace App\Livewire\Dashboard;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManageGroup extends Component
{
    public $selectedGroupId;

    public $name = '';

    public $shortname = '';

    public $zip = '';

    public $started_at = '';

    public $ended_at = '';

    public $editingGroupId = null;

    public $showForm = false;

    public function mount()
    {
        // Auto-select the first group if user has groups
        $firstGroup = Auth::user()->groups->first();
        if ($firstGroup) {
            $this->selectedGroupId = $firstGroup->id;
            $this->loadGroup($firstGroup->id);
        }
    }

    public function render()
    {
        $userGroups = Auth::user()->groups;

        return view('livewire.dashboard.manage-group', [
            'userGroups' => $userGroups,
        ]);
    }

    public function loadGroup($groupId)
    {
        $group = Auth::user()->groups()->findOrFail($groupId);

        $this->selectedGroupId = $group->id;
        $this->editingGroupId = $group->id;
        $this->name = $group->name;
        $this->shortname = $group->shortname;
        $this->zip = $group->zip ?? '';
        $this->started_at = $group->started_at?->format('Y-m-d') ?? '';
        $this->ended_at = $group->ended_at?->format('Y-m-d') ?? '';
        $this->showForm = false;
    }

    public function edit()
    {
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'shortname' => 'required|string|max:255|unique:groups,shortname,'.$this->editingGroupId,
            'zip' => 'nullable|string|max:255',
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
        ]);

        $group = Auth::user()->groups()->findOrFail($this->editingGroupId);

        $group->update([
            'name' => $this->name,
            'shortname' => $this->shortname,
            'zip' => $this->zip ?: null,
            'started_at' => $this->started_at ?: null,
            'ended_at' => $this->ended_at ?: null,
        ]);

        $this->showForm = false;
        $this->dispatch('group-saved');
    }

    public function cancel()
    {
        if ($this->editingGroupId) {
            $this->loadGroup($this->editingGroupId);
        }
        $this->showForm = false;
    }

    public function updatedSelectedGroupId($value)
    {
        if ($value) {
            $this->loadGroup($value);
        }
    }
}
