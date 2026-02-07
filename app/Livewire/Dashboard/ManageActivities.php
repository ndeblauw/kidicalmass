<?php

namespace App\Livewire\Dashboard;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManageActivities extends Component
{
    public $title = '';

    public $description = '';

    public $location = '';

    public $begin_date = '';

    public $end_date = '';

    public $editingActivityId = null;

    public $showForm = false;

    public function mount()
    {
        //
    }

    public function render()
    {
        $userGroups = Auth::user()->groups()->pluck('groups.id');

        $activities = Activity::whereHas('groups', function ($query) use ($userGroups) {
            $query->whereIn('groups.id', $userGroups);
        })->with(['groups', 'author'])->latest()->get();

        return view('livewire.dashboard.manage-activities', [
            'activities' => $activities,
        ]);
    }

    public function toggleForm()
    {
        $this->showForm = ! $this->showForm;
        if (! $this->showForm) {
            $this->reset(['title', 'description', 'location', 'begin_date', 'end_date', 'editingActivityId']);
        }
    }

    public function create()
    {
        $this->reset(['title', 'description', 'location', 'begin_date', 'end_date', 'editingActivityId']);
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'begin_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:begin_date',
        ]);

        $userGroups = Auth::user()->groups()->pluck('groups.id');

        if ($this->editingActivityId) {
            $activity = Activity::whereHas('groups', function ($query) use ($userGroups) {
                $query->whereIn('groups.id', $userGroups);
            })->findOrFail($this->editingActivityId);

            $activity->update([
                'title' => $this->title,
                'description' => $this->description,
                'location' => $this->location,
                'begin_date' => $this->begin_date,
                'end_date' => $this->end_date,
            ]);
        } else {
            $activity = Activity::create([
                'title' => $this->title,
                'description' => $this->description,
                'location' => $this->location,
                'begin_date' => $this->begin_date,
                'end_date' => $this->end_date,
                'author_id' => Auth::id(),
            ]);

            // Attach to user's groups
            $activity->groups()->attach($userGroups);
        }

        $this->reset(['title', 'description', 'location', 'begin_date', 'end_date', 'editingActivityId', 'showForm']);
        $this->dispatch('activity-saved');
    }

    public function edit($activityId)
    {
        $userGroups = Auth::user()->groups()->pluck('groups.id');

        $activity = Activity::whereHas('groups', function ($query) use ($userGroups) {
            $query->whereIn('groups.id', $userGroups);
        })->findOrFail($activityId);

        $this->editingActivityId = $activity->id;
        $this->title = $activity->title;
        $this->description = $activity->description;
        $this->location = $activity->location;
        $this->begin_date = $activity->begin_date?->format('Y-m-d\TH:i');
        $this->end_date = $activity->end_date?->format('Y-m-d\TH:i');
        $this->showForm = true;
    }

    public function delete($activityId)
    {
        $userGroups = Auth::user()->groups()->pluck('groups.id');

        $activity = Activity::whereHas('groups', function ($query) use ($userGroups) {
            $query->whereIn('groups.id', $userGroups);
        })->findOrFail($activityId);

        $activity->delete();

        $this->dispatch('activity-deleted');
    }

    public function cancel()
    {
        $this->reset(['title', 'description', 'location', 'begin_date', 'end_date', 'editingActivityId', 'showForm']);
    }
}
